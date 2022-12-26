require('./bootstrap');

import Vue from 'vue';
window.Vue = Vue;

//Vuetify Plugins
import Vuetify from 'vuetify'
Vue.use(Vuetify);

//Theme Color
import colors from 'vuetify/lib/util/colors'

//Vuex
import store from './store/index'

//Routes
import  routes  from './routes'

//Frontend validation
import './validate'

//Helper function
import './prototypes'

const app = new Vue({
    el: '#app',
    router: routes,
    vuetify: new Vuetify({
        theme: {
            dark: false,
            themes: {
              light: {
                primary: colors.blue.darken1,
                secondary: colors.grey.darken1,
                accent: colors.shades.black,
                error: colors.red.accent3,
              },
              dark: {
                primary: colors.blue.lighten1,
              },
            },
        },
    }),
    store
})