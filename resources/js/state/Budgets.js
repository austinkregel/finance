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
            state.budgets = {
                loading: true,
            };
            const { data: budgets } = await axios.get(buildUrl('/api/budgets', {
                include: 'tags.transactions',
            }));

            budgets.data = await Promise.all(budgets.data.map(async (budget) => {
                let { data } = await axios.get(buildUrl('/api/budgets/'+budget.id+'/total_spends'));
                budget.total_spend = data;
                return budget;
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
