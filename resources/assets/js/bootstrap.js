import Vue from 'vue';
import VueRouter from 'vue-router';
import Vuex from 'vuex';
import toaster from './components/toaster';
import axios from 'axios';

window.Vue = Vue;

Vue.use(VueRouter);
Vue.use(Vuex);

Vue.component('fs-toaster', toaster);

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
