const { findLocalStorage, setLocalStorage, initLocalStorage } = require('./LocalStorage');

initLocalStorage('darkMode', false);

export default {
    state: {
        user: {},
        groups: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
        alerts: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
        transactions: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
        accounts: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
        selectedTransactions: {},
        darkMode: findLocalStorage('darkMode'),
    },
    getters: {
        user: (state) => state.user,
        groups: (state) => state.groups,
        groupsById: (state) => state.groups.data.reduce((groups, group) => ({
            ...groups,
            [group.id]: group,
        }), {}),
        alerts: (state) => state.alerts,
        transactions: (state) => state.transactions,
        accounts: (state) => state.accounts,
        accountsById: (state) => state.accounts.data.reduce((accounts, account) => ({
            ...accounts,
            [account.account_id]: account,
        }), {}),
        selectedTransactions: (state) => state.selectedTransactions,
        darkMode: (state) => state.darkMode
    },
    mutations: {
        transactionLoading(state, value) {
            state.transactions.loading = value;
        },
        select(state, transaction) {
            if (state.selectedTransactions[transaction.id]) {
                const transactions = state.selectedTransactions;
                delete transactions[transaction.id];
                state.selectedTransactions = {
                    ...transactions
                }
                return;
            }

            state.selectedTransactions = {
                ...state.selectedTransactions,
                [transaction.id]: transaction
            };
        },
        user(state, user) {
            state.user = user;
        },
        toggleDarkmode(state) {
            state.darkMode = !state.darkMode;
            setLocalStorage('darkMode', state.darkMode);
        }
    },
    actions: {
        async fetchTransactions({ dispatch, state, commit, getters }, { page = 1,  }) {
            state.transactions.loading = true;

            const {data: transactions} = await axios.get(buildUrl('/abstract-api/transactions', {
                filter: {
                    account_id: 'in:' + getters.accounts.data.map(account => account.account_id).join(',')
                },
                sort: '-date',
                page,
                include: 'categories,category,tags',
            }));


            state.transactions = {
                ...transactions,
                data: state.transactions.data.concat(transactions.data),
                loading: false,
            }
        },
        async fetchUser({ state }) {
            const { data: user } = await axios.get(buildUrl('/api/user'));

            state.user = user;
        },
        async runAction({ getters }, { action, data }) {
            await axios.post(buildUrl('/api/actions/' + action), data);
        },
        async fetchAccounts({ state }) {
            const { data: accounts } = await axios.get(buildUrl('/api/accounts', {
                action: 'paginate:100',
                include: 'institution,token'
            }));

            state.accounts = accounts;
        },
        async fetchGroups({ dispatch, state, commit }) {
            const { data: groups } = await axios.get(buildUrl('/abstract-api/groups', {
                include: 'conditionals',
            }));

            state.groups = {
                ...groups,
                loading: false,
            };
        },
        async saveGroup({ state, dispatch }, { name, conditions}) {
            try {
                const {data: group} = await axios.post('/api/tags', {
                    name
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
            const updateName = original.name === updated.name;

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
                if (updateName) {
                    await axios.post('/api/tags/' + original.id, {
                        name: updated.name
                    })
                }

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
                if (updateName) {
                    const { conditionals, ... updatedData } = updated;
                    await axios.put('/api/alerts/' + original.id, {
                        ...updatedData
                    })
                }

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
