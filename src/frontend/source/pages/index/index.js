import axios from "axios";
import Vue from "vue";
import VueAxios from "vue-axios";
import VueRouter from "vue-router";
import Vuex from "vuex";
import "moment";
import "./filters";
import "./index.scss";
import "./../../plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js";
import "./../../plugins/bower_components/sweetalert/sweetalert.js";
import navbar from "./templates/modules/navbar.vue";
import footer from "./templates/modules/footer.vue";

import routes from "./routes";

(function ($) {

  axios.defaults.baseURL = "/api";

  require("./axios")(axios);


  navigator.getUserMedia = ( navigator.getUserMedia ||
    navigator.webkitGetUserMedia ||
    navigator.mozGetUserMedia ||
    navigator.msGetUserMedia);

  Vue.use(VueAxios, axios);
  Vue.use(Vuex); 
  Vue.use(VueRouter);
  Vue.component("navbar", navbar);
  Vue.component("footer-box", footer);

  const router = new VueRouter({
    routes
  });

  const store = new Vuex.Store(require("./store"));

  /* eslint-disable no-new */
  new Vue({
    el: "#wrapper",
    router,
    store,
  });

})(window.jQuery);

require("./../../common/js/custom.min.js");
