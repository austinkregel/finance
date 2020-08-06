export default {
    state: {
        budgets: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
    },
    getters: {
        budgets: (state) => state.budgets,
        budgetsById: (state) => state.budgets.data.reduce((budgets, budget) => ({
            ...budgets,
            [budget.id]: budget,
        }), {}),
    },
    actions: {
        async fetchBudgets({ dispatch, state, commit }) {
            const { data: budgets } = await axios.get(buildUrl('/abstract-api/budgets', {
                include: 'tags',
                filter: {
                    totalSpends: true,
                }
            }));

            state.budgets = {
                ...budgets,
                loading: false,
            };
        },
        async saveBudget({ state, dispatch }, formData) {
            try {
                const { data: budget } = await axios.post('/abstract-api/budgets', formData)
            } catch (e) {
                console.error(e);
            } finally {
                await dispatch('fetchBudgets')
            }
        },
        async updateBudget({ state, dispatch }, { original, updated, tags }) {
            try {
                await axios.put('/abstract-api/budgets/' + original.id, updated)
                await axios.put('/api/budgets/' + original.id +'/tags', tags);
            } catch (e) {
                console.error(e);
            } finally {
                await dispatch('fetchBudgets')
            }
        }
    }
}
