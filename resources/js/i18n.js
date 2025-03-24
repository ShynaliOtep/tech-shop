import {createI18n} from 'vue-i18n';

import ru from './locales/ru.json';
import en from './locales/en.json';

const userLang = localStorage.getItem('lang') || 'ru'

const i18n = createI18n({
    legacy: false,
    locale: userLang,
    fallbackLocale: 'ru',
    messages: {ru, en}
})


export default i18n;
