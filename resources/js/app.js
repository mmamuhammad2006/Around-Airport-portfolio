/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');


window.Vue = require('vue').default;

import mixin from './mixin/mixin.js';

Vue.mixin(mixin);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('google-adsense', require('./components/google-adsense/index.vue').default);
Vue.component('airports-management', require('./components/airports/index.vue').default);
Vue.component('airports-code-detail', require('./components/airports/code/index.vue').default);
Vue.component('airports-code-category-detail', require('./components/airports/code/category/index.vue').default);
Vue.component('airports-code-business-detail', require('./components/airports/code/business/index.vue').default);
Vue.component('loader', require('./components/loader.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


const app = new Vue({
    el: "#app",
    data: {
        isLoading: false,
    },
    methods:{
        startLoading(){
            this.isLoading = true;
        },
        stopLoading(){
            this.isLoading = false;
        }
    }
});
