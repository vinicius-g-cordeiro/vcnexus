/**
 * @brief Router for web application 
 * @author Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 * @version 1.0.0
 * @date 2026/08/29
 * @copyright Copyright (c) 2026 - Vinicius Goncalves Cordeiro <vinicordeirogo@gmail.com>
 */

import { createI18n } from 'vue-i18n'


const i18n = createI18n({
    legacy: false,
    locale: 'pt-BR',
    fallbackLocale: 'pt-BR',
    messages: {
        'pt-BR': import('./locales/shared/pt-BR.json'),
        'en-US': import('./locales/shared/en-US.json')
    }
})

export default i18n