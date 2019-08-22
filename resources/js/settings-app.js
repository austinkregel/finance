import Vue from 'vue'
import VueRouter from 'vue-router'

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = Vue;
Vue.use(VueRouter);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */
const LinkPlaid = require('./settings/Plaid/LinkAccount').default;
const Settings = require('./settings/Settings').default;
console.log(process.env)

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
                redirect: "/plaid"
            },
            {
                path: '/plaid',
                component: LinkPlaid
            }
        ]
    },
];
const router = new VueRouter({
    routes
});
router.replace({ redirect: '/plaid' })

const app = new Vue({
    router,
}).$mount('#app');
