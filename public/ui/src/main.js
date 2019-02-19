import Vue from 'vue';
import {
  Row, Col, Select, Option,
} from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import App from './App.vue';

Vue.config.productionTip = false;

Vue.use(Row);
Vue.use(Col);
Vue.use(Select);
Vue.use(Option);

new Vue({
  render: h => h(App),
}).$mount('#ui-missing');
