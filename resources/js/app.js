import { createApp } from "vue";
import { createPinia } from "pinia";
import CartPage from "./pages/CartPage.vue";
import i18n from "./i18n.js";
import ImageCarousel from "./components/ImageCarousel.vue";
import BonusPage2 from "./pages/BonusPage2.vue";
import BonusPageNew from "./pages/BonusPageNew.vue";
import CartPage2 from "./pages/CartPage2.vue";
import "vue-datepicker-next/index.css";
import SignaturePage from "./pages/SignaturePage.vue";
import AgreementPage from "./pages/Agreement.vue";
import RentBooking from "./pages/RentBooking.vue";

const pinia = createPinia();

const components = {
    "cart-page": CartPage, // Добавь сюда другие компоненты, если нужно
    carousel: ImageCarousel,
    "bonus-page": BonusPage2,
    "cart-page2": CartPage2,
    "bonus-page-new": BonusPageNew,
    "signature-page": SignaturePage,
    "agreement-page": AgreementPage,
    "rent-booking": RentBooking,
};

document.addEventListener("DOMContentLoaded", () => {
    Object.keys(components).forEach((selector) => {
        document.querySelectorAll(selector).forEach((el) => {
            // Пропсы из Blade передаются JSON-ом в data-props.
            const props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
            const app = createApp(components[selector], props);
            app.use(i18n); // Подключаем i18n
            app.use(pinia);
            app.mount(el);
        });
    });
});
