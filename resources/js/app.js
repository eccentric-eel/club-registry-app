import { createApp }                      from 'vue';
import { createWebHistory, createRouter } from 'vue-router';

import App         from './app.vue';
import IndexPage   from './components/IndexPage.vue';
import LoginPage   from './components/LoginPage.vue';
import RecordsPage from './components/RecordsPage.vue';

import Datepicker from 'vue3-datepicker'

const routes = [ { path: '/',              component: IndexPage   },
                 { path: '/admin',         component: LoginPage   },
                 { path: '/admin/records', component: RecordsPage } ];        

const app     = createApp(App);
const router  = createRouter({ history: createWebHistory(), routes });

app.component('Datepicker', Datepicker);

app.use(router);
app.mount('#app');