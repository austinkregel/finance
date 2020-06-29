import Vue from 'vue'
import VueRouter from 'vue-router'
import HistoricalSync from "./settings/Plaid/HistoricalSync";
import {initLocalStorage, setLocalStorage} from "./LocalStorage";
import Vuex from "vuex";
import VueToasted from 'vue-toasted';
import Zondicon from 'vue-zondicons';

Vue.component('zondicon', Zondicon);

initLocalStorage('darkMode', false);

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
                path: '/failed-jobs',
                component: FailedJobs,
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
}).$mount('#app');
