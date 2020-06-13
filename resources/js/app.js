/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
const { buildUrl } = require('@kbco/query-builder')
import Zondicon from 'vue-zondicons';
import Vuex from 'vuex';
import VueRouter from 'vue-router';
import dayjs from 'dayjs';
import locale from 'dayjs/plugin/localizedFormat'

dayjs.extend(locale)

dayjs.extend(require('./FormatToLocaleTimezone').default)

window.buildUrl = buildUrl
window.dayjs = dayjs

window.request = async (type, url, options) => {
    if (type.toLowerCase() === 'get') {
        var { data } = await axios.get(buildUrl(url, options))
    } else {
        var { data } = await axios[type](url, options)
    }

    if (data.hasOwnProperty('data')) {
        return data.data;
    }

    return data;
}

Vue.component('zondicon', Zondicon);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./components', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.use(Vuex);
Vue.use(VueRouter);

const store = new Vuex.Store({
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
    },
    getters: {
        user: (state) => state.user,
        groups: (state) => state.groups,
        transactions: (state) => state.transactions,
        accounts: (state) => state.accounts,
        accountsById: (state) => state.accounts.data.reduce((accounts, account) => ({
            ...accounts,
            [account.account_id]: account,
        }), {}),
        selectedTransactions: (state) => state.selectedTransactions
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
        }
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
        async fetchTransactions({ dispatch, state, commit, getters }, { page = 1,  }) {
            state.transactions.loading = true;

            try {
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
            } catch (e) { console.log('faided to transact', e) }
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
        }
    }
})

const router = new VueRouter({
    routes: [
        {
            path: '/',
            component: require('./routes/BaseRoute').default,
            props: true,
            children: [
                {
                    path: '/',
                    component: require('./routes/Transactions').default,
                    props: true,
                },
                {
                    path: '/groups',
                    component: require('./routes/TransactionGroups').default,
                    props: true,
                },
                {
                    path: '/graphs',
                    component: require('./routes/CoolGraphs').default,
                    props: true,
                },
                {
                    path: '/alerts',
                    component: require('./routes/TransactionAlerts').default,
                    props: true,
                },
                {
                    path: '*',
                    component: require('./routes/404').default,
                    props: true,
                }
            ]
        }
    ]
})
const start = async () => {
    await store.dispatch('fetchUser')
    await store.dispatch('fetchAccounts')
    store.dispatch('fetchGroups')

    const app = new Vue({
        el: '#app',
        store,
        router
    });
}

start();
