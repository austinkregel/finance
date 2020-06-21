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
require('./dark-mode-directives')
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

const store = new Vuex.Store(require('./store').default)

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
    const app = new Vue({
        el: '#app',
        store,
        router,
        computed: {
            darkMode() {
                return window.darkMode;
            }
        },
        async mounted() {
            try {
                await store.dispatch('fetchUser')
                await store.dispatch('fetchAccounts')
                store.dispatch('fetchGroups')
                store.dispatch('fetchAlerts')
            } catch (e) {
                console.error(e)
            }

        }
    });
}

start();
