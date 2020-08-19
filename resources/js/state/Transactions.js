export default {
    state: {
        transactions: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        }
    },
    getters: {
        transactions: (state) => state.transactions,
    },
    mutations: {
        transactionLoading(state, value) {
            state.transactions.loading = value;
        }
    },
    actions: {
        async fetchTransactions({ dispatch, state, commit, getters }, { page = 1, name, }) {
            state.transactions.loading = true;

            const { data: transactions } = await axios.get(buildUrl('/abstract-api/transactions', {
                filter: Object.assign({
                    account_id: 'in:' + getters.accounts.data.map(account => account.account_id).join(',')
                }, name ? {
                    name
                } : {}),
                sort: '-date',
                page,
                include: 'categories,category,tags',
            }));


            state.transactions = {
                ...transactions,
                data: state.transactions.data.concat(transactions.data),
                loading: false,
            }
        }
    }
}
