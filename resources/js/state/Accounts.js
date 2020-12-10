const { buildUrl } = require('@kbco/query-builder');
export default {
    state: {
        accounts: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        },
    },
    mutations: {
        setAccountLoading(state, loading) {
            state.accounts.loading = loading;
        }
    },
    getters: {
        accounts: (state) => state.accounts,
        accountsById: (state) => state.accounts.data.reduce((accounts, account) => ({
            ...accounts,
            [account.account_id]: account,
        }), {}),
    },
    actions: {
        async fetchAccounts({ state, rootGetters }) {
            const { data: accounts } = await axios.get(buildUrl('/api/accounts', {
                action: 'paginate:100',
                include: 'institution,token'
            }));

            state.accounts = {
                ...accounts,
                loading: false,
            };
        }
    }
}
