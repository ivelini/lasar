import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

import './assets/global_assets/css/icons/icomoon/styles.min.css'
import './assets/css/all.min.css'
import 'bootstrap/dist/css/bootstrap.min.css'

createApp(App)
    .use(router)
    .use(store)
    .mount('#app')
