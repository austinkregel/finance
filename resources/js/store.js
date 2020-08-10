const { findLocalStorage, setLocalStorage, initLocalStorage } = require('./LocalStorage');

initLocalStorage('darkMode', false);

export default {
    modules: {
        accounts: require('./state/Accounts').default,
        alerts: require('./state/Alerts').default,
        actions: require('./state/Actions').default,
        authentication: require('./state/Authentication').default,
        budgets: require('./state/Budgets').default,
        groups: require('./state/Groups').default,
        transactions: require('./state/Transactions').default,
    },
    state: {
        notifications: [],
        selectedTransactions: {},
        darkMode: findLocalStorage('darkMode'),
    },
    getters: {
        selectedTransactions: (state) => state.selectedTransactions,
        darkMode: (state) => state.darkMode,
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
        async purgeCache() {
            try {
                await axios.post('/api/cache-clear');
                app.$toasted.success('Your cache was cleared!', {
                    position: 'bottom-right',
                    theme: "custom"
                })
            } catch (e) {
                app.$toasted.success('We ran into an error when purging your cache!')
            }
        }
    }
}
