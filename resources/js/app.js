import { createApp }                      from 'vue';
import { createWebHistory, createRouter } from 'vue-router';

import App          from './app.vue';
import IndexPage    from './components/IndexPage.vue';
import LoginPage    from './components/LoginPage.vue';
import RecordsPage  from './components/RecordsPage.vue';
import RecordModal  from './components/RecordModal.vue';
import LoadingModal from './components/LoadingModal.vue';

import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';

const routes = [ { path: '/',              component: IndexPage   },
                 { path: '/admin',         component: LoginPage   },
                 { path: '/admin/records', component: RecordsPage } ];        

const app     = createApp(App);
const router  = createRouter({ history: createWebHistory(), routes });

app.component('VueCtkDateTimePicker', VueCtkDateTimePicker);
app.component('RecordModal',          RecordModal);
app.component('LoadingModal',         LoadingModal);

app.use(router);
app.mount('#app');