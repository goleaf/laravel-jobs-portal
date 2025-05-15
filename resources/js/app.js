/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import '../assets/js/currency.js';

// Third-party libraries
import 'jquery';
import 'bootstrap';
import 'slick-carousel';
import 'intl-tel-input';
import 'intl-tel-input/build/js/utils.js';
import 'autonumeric';
import 'quill';

// Custom JS
import '../assets/js/turbo';
import '../assets/js/custom/helpers';
import '../assets/js/custom/custom';

// Import the CSS files
import 'intl-tel-input/build/css/intlTelInput.css';
import 'timepicker/jquery.timepicker.min.css';
import 'quill/dist/quill.snow.css';
import 'quill/dist/quill.bubble.css';

// Include Vue
import Vue from 'vue';
window.Vue = Vue;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
