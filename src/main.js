import Vue from 'vue'
import App from './App'
import router from './router'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import axios from 'axios'
import 'webpack-material-design-icons'
import 'izitoast/dist/css/iziToast.min.css'
import VueHead from 'vue-head'
import '@fortawesome/fontawesome-free/css/all.css'
import VueCarousel from 'vue-carousel'
import Msj from './plugins/Msj'
import VueApexCharts from 'vue-apexcharts'

Vue.config.productionTip = false

const vuetifyOptions = { }
Vue.use(Vuetify)
Vue.use(VueHead);
Vue.config.productionTip = false
axios.defaults.withCredentials = true
Vue.use(VueCarousel);
Vue.use(Msj);
Vue.use(VueApexCharts);

Vue.component('apexchart', VueApexCharts)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  vuetify: new Vuetify(vuetifyOptions),
  router,
  components: { App },
  template: '<App/>'
})
