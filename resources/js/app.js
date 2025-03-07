import { createApp } from 'vue';
import CartPage from './components/CartPage.vue';
import i18n from './i18n.js'

const components = {
    'cart-page': CartPage,  // Добавь сюда другие компоненты, если нужно
};

document.addEventListener("DOMContentLoaded", () => {
    Object.keys(components).forEach((selector) => {
        document.querySelectorAll(selector).forEach((el) => {
            const app = createApp(components[selector]);
            app.use(i18n); // Подключаем i18n
            app.mount(el);
            console.log(`Vue смонтирован на ${selector}`);
        });
    });
});
