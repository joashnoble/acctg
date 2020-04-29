
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('select2');

window.Vue = require('vue')
window.moment = require('moment')
window.printd = require('printd')

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import App from './App.vue'
import router from './router'
import FormMixin from './mixins/FormMixin'
import axios from 'axios'
import Notifications from 'vue-notification'
import NProgress from 'nprogress';
import 'vue-awesome/icons/flag'
import 'vue-awesome/icons'
import Icon from 'vue-awesome/components/Icon'
import store from './store'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'nprogress/nprogress.css'
import DatePicker from 'vue2-datepicker'
import BarExample from './views/charts/BarExample'
import LineExample from './views/charts/LineExample'
import cSwitch from './components/Switch'
import CKEditor from 'ckeditor4-vue';

Vue.use( CKEditor );
Vue.use(Notifications)
Vue.use(DatePicker)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

Vue.component('icon', Icon)
Vue.component('LineExample', LineExample)
Vue.component('BarExample', BarExample)
Vue.component('cSwitch', cSwitch)

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
const axiosConfig = {
  baseURL: 'http://accounting.test',
  timeout: 30000
}

Vue.component('icon', Icon)
Vue.component('App', require('./App.vue'))
Vue.component('select2', require('./select2/Sele\ct2'))
Vue.component('typeahead', require('./typeahead/typeahead'))
Vue.component('vue-autonumeric', require('vue-autonumeric'))
window.$ = window.jQuery = require('jquery')
Vue.use(BootstrapVue)

Vue.prototype.$http = axios.create(axiosConfig)
// before a request is made start the nprogress
Vue.prototype.$http.interceptors.request.use(config => {
  NProgress.start()
  return config
})

// before a response is returned stop nprogress
Vue.prototype.$http.interceptors.response.use(response => {
  NProgress.done()
  return response
}, error => {
  if(error.response.status === 401 || error.response.status === 403){
    store.commit('logoutUser')
    localStorage.setItem('token', '')
    router.push({name: 'Login'})
  }
  NProgress.done()
  return Promise.reject(error)
})
Vue.mixin(FormMixin)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: {
    App
  }
})
