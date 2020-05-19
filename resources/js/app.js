/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.axios=require('axios');

import { BootstrapVue } from 'bootstrap-vue'
Vue.use(BootstrapVue)
import 'bootstrap-vue/dist/bootstrap-vue.css'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


export const bus = new Vue();
//Vue.component('payments-table', require('./components/PaymentsTable.vue').default);
Vue.component('all-carts', require('./components/carts/allCarts.vue').default);
Vue.component('cart', require('./components/carts/cart.vue').default);
Vue.component('add-cart', require('./components/carts/addCart.vue').default);
Vue.component('line-chart-container', require('./components/stats/charts/lineChartContainer.vue').default);
Vue.component('pie-chart-container', require('./components/stats/charts/pieChartContainer.vue').default);
Vue.component('stats', require('./components/stats/stats.vue').default);
Vue.component('payments', require('./components/payments/allPayments.vue').default);
Vue.component('payment-group', require('./components/payments/paymentGroup.vue').default);
Vue.component('add-payment', require('./components/payments/addPayment.vue').default);
Vue.component('payments-table', require('./components/payments/paymentTable.vue').default);
Vue.component('show-payment', require('./components/payments/showPayment.vue').default);
Vue.component('notification', require('./components/notification.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//import PaymentsTable from './components/PaymentsTable'
const app = new Vue({
    el: '#app',
    data: {
        payment_types: {}
    },
    components: {
    },
});

//popovers stats.index
$(function () {
    $('[data-toggle="popover"]').popover()
})

$('.carousel').carousel()