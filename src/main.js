import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import "./plugins/element.js";
import vuetify from "./plugins/vuetify";

Vue.config.productionTip = false;

const baseURL = "/";

fetch(baseURL + "config/home.json" + "?t=" + Date.now())
  .then(response => response.json())
  .then(info => {
    Vue.prototype.$info = info;
    new Vue({
      router,
      store,
      vuetify,
      render: h => h(App)
    }).$mount("#app");
  });
