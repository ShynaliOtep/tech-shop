import { createI18n } from "vue-i18n";

import ru from "./locales/ru.json";
import en from "./locales/en.json";

// Язык выбирается на сервере (сессия, /change-lang) и попадает в <html lang>.
const userLang =
    document.documentElement.lang || localStorage.getItem("lang") || "ru";

const i18n = createI18n({
    legacy: false,
    locale: userLang,
    fallbackLocale: "ru",
    messages: { ru, en },
});

export default i18n;
