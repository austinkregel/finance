import Vue from 'vue'
import VueRouter from 'vue-router'
import HistoricalSync from "./settings/Plaid/HistoricalSync";
import {initLocalStorage, setLocalStorage} from "./LocalStorage";
import Vuex from "vuex";
import VueToasted from 'vue-toasted';
import Zondicon from 'vue-zondicons';
import Notifications from "./settings/Notifications";
import dayjs from "dayjs";
import relativeTime from "dayjs/plugin/relativeTime";
import locale from "dayjs/plugin/localizedFormat";
import ActivityLog from "./settings/ActivityLog";
import { buildUrl } from '@kbco/query-builder';
import compromise from './compromise';

window.compromise = compromise
window.buildUrl = buildUrl
Vue.component('zondicon', Zondicon);

initLocalStorage('darkMode', false);
dayjs.extend(require('dayjs/plugin/utc'))

dayjs.extend(relativeTime)
dayjs.extend(locale)
dayjs.extend(require('./FormatToLocaleTimezone').default)
window.dayjs = dayjs
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = Vue;
Vue.use(VueRouter);
Vue.use(VueToasted, {
    position: 'bottom-right',
    theme: "custom"
});
require('./dark-mode-directives')

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
const LinkPlaid = require('./settings/Plaid/LinkAccount').default;
const Settings = require('./settings/Settings').default;
const AccountRow = require('./settings/Plaid/AccountRow').default;
const AlertChannels = require('./settings/AlertChannels').default;
const FailedJobs = require('./settings/FailedJobs').default;

Vue.component('account-row', AccountRow);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const routes = [
    {
        path: '/',
        component: Settings,
        name: 'Settings',
        children: [
            {
                path: '/',
                redirect: "/plaid",
                props: true,
            },
            {
                path: '/plaid',
                component: LinkPlaid,
                props: true,
            },
            {
                path: '/historical-sync',
                component: HistoricalSync,
                props: true,
            },
            {
                path: '/alert-channels',
                component: AlertChannels,
                props: true,
            },
            {
                path: '/notifications',
                component: Notifications,
                props: true,
            },
            {
                path: '/failed-jobs',
                component: FailedJobs,
                props: true,
            },
            {
                path: '/activity-log',
                component: ActivityLog,
                props: true,
            }
        ]
    },
];
const router = new VueRouter({
    routes
});

window.Bus = new Vue;
const store = new Vuex.Store(require('./store').default)

window.app = new Vue({
    router,
    store,
    el: '#app',
    async mounted() {
        await store.dispatch('fetchUser');
        await store.dispatch('fetchActivities', {
            filter: {
                log_name: 'access_token'
            }
        })
    }
});
