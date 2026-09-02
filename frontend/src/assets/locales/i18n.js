import { createI18n } from 'vue-i18n'

import en from './en.js'
import ptBR from './pt-BR'

const i18n = createI18n({
    legacy: false,
    locale: 'pt-BR',
    fallbackLocale: 'en',

    messages: {
        en,
        'pt-BR': ptBR,
    },
})

export default i18n