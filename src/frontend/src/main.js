import axios from "axios";
import Vue from "vue";
import Tabs from 'vue-tabs-component';
import VueAxios from "vue-axios";
import VueRouter from "vue-router";
import Vuex from "vuex";
import "moment";
import "./filters";
import RouterPrefetch from 'vue-router-prefetch'
import App from "./App";
// TIP: change to import router from "./router/starterRouter"; to start with a clean layout
import router from "./router/index";
import Axios from "./axios";

import BlackDashboard from "./plugins/blackDashboard";
import i18n from "./i18n"
import './registerServiceWorker'

axios.defaults.baseURL = "/api";

Axios(axios);

Vue.use(Tabs);
Vue.use(BlackDashboard);
Vue.use(VueRouter);
Vue.use(RouterPrefetch);
Vue.use(VueAxios, Axios);
Vue.use(Vuex);

/* eslint-disable no-new */

const store = new Vuex.Store(require("./store"));

new Vue({
  router,
  i18n,
  store,
  render: h => h(App)
}).$mount("#app");
