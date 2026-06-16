<template>
    <div>
        <div class="bonus-page">
            <div class="bonus-page-left">
                <p class="bonus-faq-title">Ответы на вопросы</p>
                <div class="faq-list">
                    <FaqItem
                        v-for="(item, index) in faqs"
                        :key="index"
                        :question="item.question"
                        :answer="item.answer"
                    />
                </div>
            </div>
            <div class="bonus-page-right">
                <div
                    class="bonus-level-block"
                    style="
                        background: linear-gradient(
                            to bottom,
                            #191e22,
                            #0a0c0e
                        );
                    "
                >
                    <p class="title">
                        <img
                            v-if="level < 1"
                            src="/img/locked.svg"
                            alt="icon"
                            class="lock-icon"
                        />
                        <span>Black</span>
                    </p>
                    <p class="bonus">{{ bonusSum }} / 50 000 ₸</p>
                    <div class="text-block">
                        <p class="title">Скидка 5%</p>
                        <p class="text">с каждой аренды</p>
                    </div>
                    <div class="text-block">
                        <p class="title">При аренде от 25 000 ₸</p>
                        <p class="text">Бесплатная доставка на одну сторону</p>
                    </div>
                    <a
                        href="https://wa.me/77011234567?text=Здравствуйте,%20интересует%20бонусный%20уровень%20Black"
                        target="_blank"
                        class="btn-bonus"
                        >Подключить</a
                    >
                </div>
                <br />
                <div
                    class="bonus-level-block"
                    style="
                        background: linear-gradient(
                            to bottom,
                            #3c2b1f,
                            #18100d
                        );
                    "
                >
                    <p class="title">
                        <img
                            v-if="level < 2"
                            src="/img/locked.svg"
                            alt="icon"
                            class="lock-icon"
                        />
                        <span>Bronze</span>
                    </p>
                    <p class="bonus">{{ bonusSum }} / 500 000 ₸</p>
                    <div class="text-block">
                        <p class="title">Скидка 10%</p>
                        <p class="text">с каждой аренды</p>
                    </div>
                    <div class="text-block">
                        <p class="title">При аренде от 25 000 ₸</p>
                        <p class="text">Бесплатная доставка на одну сторону</p>
                    </div>
                    <a
                        href="https://wa.me/77011234567?text=Здравствуйте,%20интересует%20бонусный%20уровень%20Bronze"
                        target="_blank"
                        class="btn-bonus"
                        >Подключить</a
                    >
                </div>
                <br />
                <div
                    class="bonus-level-block"
                    style="
                        background: linear-gradient(
                            to bottom,
                            #1a231c,
                            #0a0e0b
                        );
                    "
                >
                    <p class="title">
                        <img
                            v-if="level < 3"
                            src="/img/locked.svg"
                            alt="icon"
                            class="lock-icon"
                        />
                        <span>Emerald</span>
                    </p>
                    <p class="bonus">{{ bonusSum }} / 1 500 000 ₸</p>
                    <div class="text-block">
                        <p class="title">Скидка 15%</p>
                        <p class="text">с каждой аренды</p>
                    </div>
                    <div class="text-block">
                        <p class="title">При аренде от 25 000 ₸</p>
                        <p class="text">Бесплатная доставка на одну сторону</p>
                    </div>
                    <a
                        href="https://wa.me/77011234567?text=Здравствуйте,%20интересует%20бонусный%20уровень%20Emerald"
                        target="_blank"
                        class="btn-bonus"
                        >Подключить</a
                    >
                </div>
                <br />
                <div
                    class="bonus-level-block"
                    style="
                        background: linear-gradient(
                            to bottom,
                            #260724,
                            #0f030e
                        );
                    "
                >
                    <p class="title">
                        <img
                            v-if="level < 4"
                            src="/img/locked.svg"
                            alt="icon"
                            class="lock-icon"
                        />
                        <span>Amethyst</span>
                    </p>
                    <p class="bonus">{{ bonusSum }} / 5 000 000 ₸</p>
                    <div class="text-block">
                        <p class="title">Скидка 30%</p>
                        <p class="text">с каждой аренды</p>
                    </div>
                    <div class="text-block">
                        <p class="title">При аренде от 25 000 ₸</p>
                        <p class="text">Бесплатная доставка на одну сторону</p>
                    </div>
                    <a
                        href="https://wa.me/77011234567?text=Здравствуйте,%20интересует%20бонусный%20уровень%20Amethyst"
                        target="_blank"
                        class="btn-bonus"
                        >Подключить</a
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FaqItem from "../components/bonus/FaqItem.vue";
import axios from "axios";
import auth from "../auth.js";
export default {
    components: { FaqItem },
    data() {
        return {
            faqs: [
                {
                    question: "Как получить скидку",
                    answer:
                        "Программа лояльности Pixel: чем больше покупок — тем выше скидка!</br>" +
                        "</br>" +
                        "На сайте действует система уровней, позволяющая нашим клиентам увеличивать процент скидки в зависимости от суммы совершённых покупок.</br>" +
                        "</br>" +
                        "Стартовый уровень “Black”: При накоплении покупок на сумму от 50 000 ₸ вы получаете уровень “Black” и начинаете получать скидку в размере 5% от стоимости заказов.</br>" +
                        "</br>" +
                        "Следующий уровень “Bronze”: Как только ваши траты достигнут 500 000 ₸, вы автоматически переходите на уровень “Bronze”, и ваша скидка увеличивается до 10%.</br>" +
                        "</br>" +
                        "Предпоследний уровень “Emerald”: При общей сумме покупок свыше 1 500000 ₸ вы становитесь участником уровня “Emerald” с максимальной скидкой — 15%.</br>" +
                        "</br>" +
                        "Наивысший уровень “Amethyst”: При общей сумме покупок свыше 5 000 000 ₸ вы становитесь участником уровня “Amethyst” с максимальной скидкой — 15%.</br>" +
                        "</br>" +
                        "Хотите сразу получить повышенную скидку? Вы можете купить желаемый уровень через наш отдел продаж и откройте доступ к уровням “Bronze”, “Emerald”, “Amethyst” без ожидания!</br>" +
                        "</br>" +
                        "Обратите внимание: В расчёт суммы для перехода на следующий уровень включаются только средства, оплаченные банковской картой или наличными в офисе. Платежи бонусами не учитываются. Например, если вы оплачиваете заказ на 10 000 ₸, из которых 3 000 ₸ списаны бонусами, то в зачет идёт только 7 000 ₸.",
                },
                {
                    question: "Как вывести Pixel coin",
                    answer:
                        "Вы легко можете вывести ваши бонусы на реальные деньги ! </br>" +
                        "</br>" +
                        "Вам просто нужно зайти в ваш профиль и отправить запрос на вывод бонусов! </br>" +
                        "</br>" +
                        "Запрос обрабатывается в течении нескольких рабочих дней. </br>" +
                        "</br>" +
                        "В случае одобрения, средства поступят вам на указанные вами реквизиты ! </br>" +
                        "</br>" +
                        "Важно! Конвертация Pixel coin - Тенге 2 к 1, то есть 10.000 бонусов = 5.000 тенге.",
                },
                {
                    question: "Как заработать Pixel coin (бонусы)",
                    answer:
                        "На данный момент вы можете накапливать бонусы двумя способами: </br>" +
                        "•⁠  ⁠При оформлении заказов на нашем сайте </br>" +
                        "•⁠  ⁠⁠Реферальная система",
                },
                {
                    question: "Как работает реферальная система ?",
                    answer:
                        "Наша реферальная система — это простой способ получать бонусы за то, что вы рекомендуете наш рентал своим друзьям!</br>" +
                        "</br>" +
                        "Вот как это работает:</br>" +
                        "</br>" +
                        "Поделитесь реферальной ссылкой, которая находится в вашем профиле с друзьями.</br>" +
                        "Отправьте ссылку или код друзьям через мессенджеры, соцсети или просто покажите при встрече.</br>" +
                        "</br>" +
                        "Когда ваш друг регистрируется с вашим кодом и совершает покупки, вы получаете 5% бонусов от суммы каждого заказа на свой счёт.</br>" +
                        "Больше друзей — больше бонусов!</br>" +
                        "Приглашайте сколько угодно друзей — бонусы начисляются за каждого нового покупателя.</br>",
                },
            ],
            clientData: null,
            isAuthenticated: false,
            level: 0,
            bonusSum: 0,
        };
    },
    mounted() {
        this.getClientData();
    },
    methods: {
        async getClientData() {
            const responseAuth = axios
                .get("sanctum/csrf-cookie")
                .then((response) => {
                    console.log(response);
                });

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
            console.log(csrfToken);
            axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
            const response = await axios.request({
                method: "GET",
                url: "/api/client",
                //   headers: { 'X-CSRF-TOKEN': csrfToken },
                withCredentials: true,
            });
            console.log(response.data);
            this.clientData = response.data.clientData;
            if (response.data.clientData && response.data.clientData.bonus) {
                this.level = response.data.clientData.bonus.level;
                this.bonusSum = this.formatMoney(
                    response.data.clientData.bonus.total_earned,
                );
            }
            this.isAuthenticated = response.data.isAuthenticated;
        },
        formatMoney(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        },
    },
};
</script>

<style lang="scss" scoped>
.bonus-page {
    display: flex;
    font-family:
        -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue",
        Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji",
        "Segoe UI Symbol", "Noto Color Emoji";
}
.bonus-page-left,
.bonus-page-right {
    flex: 1;
    padding: 10px;
}
.bonus-faq-title {
    font-size: 12px;
    color: #cf7f51;
}

.bonus-level-block {
    border-radius: 10px;
    color: #cf7f51;

    padding: 30px;
    .title {
        font-weight: 700;
        font-size: 20px;
        display: flex;
        margin-bottom: 0;
    }

    .bonus {
        margin-top: 3px;
        font-weight: 200;
        font-size: 16px;
    }

    .text-block {
        .title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 1px;
        }
        .text {
            margin-top: 3px;
            font-size: 16px;
            font-weight: 400;
        }
    }
    .btn-bonus {
        display: block;
        text-align: center;
        color: #cf7f51 !important;
    }
    .lock-icon {
        margin-right: 10px;
        width: 30px;
    }
}
@media (max-width: 992px) {
    .bonus-faq-title {
        font-size: 10px;
    }

    .faq-question {
        font-size: 10px;
    }

    .bonus-level-block {
        border-radius: 10px;
        padding: 15px;

        .title {
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 0;
        }

        .bonus {
            margin-top: 3px;
            font-weight: 200;
            font-size: 10px;
        }

        .text-block {
            .title {
                font-size: 10px;
                font-weight: 600;
                margin-bottom: 1px;
            }
            .text {
                margin-top: 3px;
                font-size: 10px;
                font-weight: 400;
            }
        }
        .btn-bonus {
            display: block;
            text-align: center;
            color: #cf7f51 !important;
        }
        .lock-icon {
            margin-right: 10px;
            width: 30px;
        }
    }
}
</style>
