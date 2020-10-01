export default {
    state: {
        groups: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
    },
    getters: {
        groups: (state) => state.groups,
        groupsById: (state) => state.groups.data.reduce((groups, group) => ({
            ...groups,
            [group.id]: group,
        }), {}),
    },
    actions: {
        async fetchGroups({ dispatch, state, commit }) {
            const { data: groups } = await axios.get(buildUrl('/abstract-api/groups', {
                include: 'conditionals',
            }));

            state.groups = {
                ...groups,
                loading: false,
            };
        },
        async saveGroup({ state, dispatch }, { name, conditions, must_all_conditions_pass }) {
            try {
                const {data: group} = await axios.post('/api/tags', {
                    name,
                    must_all_conditions_pass
                })

                await Promise.all(conditions.map(async ({parameter, value, comparator}) => {
                    await axios.post('/api/tags/' + group.id + '/conditionals', {
                        parameter, comparator, value,
                    })
                }))
            } catch (e) {
                console.error(e);

            } finally {
                await dispatch('fetchGroups')
            }
        },
        async updateGroup({ state, dispatch }, { original, updated }) {
            // Find the diff, update what's needed.
            // Not everything may have changed.

            const originalConditionsById = original.conditionals.reduce((conditions, condition) => ({
                ...conditions,
                [condition.id]: condition
            }), {});
            const updatedConditionsById = updated.conditionals.reduce((conditions, condition) => ({
                ...conditions,
                [condition.id]: condition
            }), {});

            console.log(updatedConditionsById);

            const tagsToCreate = updated.conditionals.filter(condition => !condition.id);

            const tagsToUpdate = Object.keys(originalConditionsById).filter(key => ([
                originalConditionsById[key].name === updatedConditionsById[key].name,
                originalConditionsById[key].comparator === updatedConditionsById[key].comparator,
                originalConditionsById[key].value === updatedConditionsById[key].value,
            ].filter(item => item).length !== 3)).reduce((conditions, key) => {
                conditions.push(updatedConditionsById[key])
                return conditions;
            }, []);

            try {
                await axios.patch('/api/tags/' + original.id, {
                    name: updated.name,
                    must_all_conditions_pass: updated.must_all_conditions_pass
                })

                await Promise.all(tagsToUpdate.map(async ({ id, parameter, comparator, value }) => {
                    await axios.put('/api/tags/' + original.id + '/conditionals/'+ id, {
                        parameter, comparator, value,
                    })
                }))

                await Promise.all(tagsToCreate.map(async ({ parameter, comparator, value }) => {
                    await axios.post('/api/tags/' + original.id + '/conditionals', {
                        parameter, comparator, value
                    })
                }))
            } catch (e) {
                console.error(e);
            } finally {
                await dispatch('fetchGroups')
            }
        },
        async deleteGroupCondition({ state, dispatch }, { tag, condition }) {
            await axios.delete('/api/tags/' + tag.id + '/conditionals/' + condition.id)
        },
    }
}
