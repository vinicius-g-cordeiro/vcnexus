/**
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from '@/routes/web'
import App from './App.vue'
import './assets/main.css'
import "bootstrap-icons/font/bootstrap-icons.css"
import "bootstrap/dist/js/bootstrap.js"
import { initDarkMode } from '@/components/darkmode.js'
import i18n from '@/assets/locales/i18n.js'
initDarkMode()
const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)
app.use(i18n)
app.mount('#app')