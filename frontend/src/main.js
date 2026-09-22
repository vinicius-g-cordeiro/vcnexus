/**
 * @brief Router for web application 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createApp } from 'vue'
import App from './App.vue'
import router from '@/router/web'
import i18n from '@/i18n'
import { createPinia } from 'pinia'
import { initDarkMode } from '@/components/darkmode.js'
initDarkMode()

const pinia = createPinia()

import '@/styles/app.css'
import 'bootstrap/dist/js/bootstrap.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css'

createApp(App).use(pinia).use(i18n).use(router).mount('#application')