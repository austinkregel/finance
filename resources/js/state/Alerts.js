export default {
    state: {
        alerts: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
    },
    getters: {
        alerts: (state) => state.alerts,
    },
    actions: {
        async fetchAlerts({ dispatch, state, commit }) {
            const { data: alerts } = await axios.get(buildUrl('/abstract-api/alerts', {
                include: 'conditionals',
            }));

            state.alerts = {
                ...alerts,
                loading: false,
            };
        },
        async saveAlert({ state, dispatch }, { conditionals, ...data }) {
            try {
                const {data: group} = await axios.post('/api/alerts', data)

                await Promise.all(conditionals.map(async ({parameter, value, comparator}) => {
                    await axios.post('/api/alerts/' + group.id + '/conditionals', {
                        parameter, comparator, value,
                    })
                }))
            } catch (e) {
                console.error(e);

            } finally {
                await dispatch('fetchAlerts')
            }
        },
        async updateAlert({ state, dispatch }, { original, updated }) {
            // Find the diff, update what's needed.
            // Not everything may have changed.
            const updateName = original.name === updated.name;

            const originalConditionsById = original.conditionals.reduce((conditions, condition) => ({
                ...conditions,
                [condition.id]: condition
            }), {});
            const updatedConditionsById = updated.conditionals.reduce((conditions, condition) => ({
                ...conditions,
                [condition.id]: condition
            }), {});

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
                const { conditionals, ...updatedData } = updated;
                await axios.put('/api/alerts/' + original.id, {
                    ...updatedData
                })


                await Promise.all(tagsToUpdate.map(async ({ id, parameter, comparator, value }) => {
                    await axios.put('/api/alerts/' + original.id + '/conditionals/'+ id, {
                        parameter, comparator, value,
                    })
                }))

                await Promise.all(tagsToCreate.map(async ({ parameter, comparator, value }) => {
                    await axios.post('/api/alerts/' + original.id + '/conditionals', {
                        parameter, comparator, value
                    })
                }))
            } catch (e) {
                console.error(e);
            } finally {
                await dispatch('fetchAlerts')
            }
        },
        async deleteAlertCondition({ state, dispatch }, { alert, condition }) {
            await axios.delete('/api/alerts/' + alert.id + '/conditionals/' + condition.id)
        }
    }
}
