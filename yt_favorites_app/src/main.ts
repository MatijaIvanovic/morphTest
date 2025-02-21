import { createApp } from 'vue'
import App from './app.vue'
import { createPinia } from 'pinia'
import router from './router'
import './style.css'


const app = createApp(App);
app.use(createPinia());
app.use(router);
app.mount('#app');


