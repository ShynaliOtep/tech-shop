<template>
    <div class="rb" ref="root">
        <div class="rb-price">
            <span class="rb-price-main">{{ formatPrice(price) }}</span>
            <span class="rb-price-unit">/сутки</span>
            <s v-if="basePrice > price" class="rb-price-old">
                {{ formatPrice(basePrice) }}
            </s>
        </div>

        <RentPeriodPicker v-model="period" :busy-days="busyDays" />

        <div class="rb-status" aria-live="polite">
            <p v-if="!ready" class="rb-status-muted">
                Выберите даты, чтобы проверить наличие и увидеть итог
            </p>
            <p v-else-if="checking" class="rb-status-muted">
                Проверяем наличие…
            </p>
            <p v-else-if="checkError" class="rb-status-red">
                Не удалось проверить наличие.
                <button
                    type="button"
                    class="rb-link"
                    @click="checkAvailability"
                >
                    Повторить
                </button>
            </p>
            <p v-else-if="free === 0" class="rb-status-red">
                На эти даты свободных нет — попробуйте другое время
            </p>
            <p v-else-if="free !== null" class="rb-status-green">
                Свободно: {{ free }} шт.
            </p>
        </div>

        <div v-if="ready && free > 0" class="rb-quantity">
            <span class="rb-quantity-label">Количество</span>
            <div class="rb-stepper">
                <button
                    type="button"
                    :disabled="quantity <= 1"
                    aria-label="Меньше"
                    @click="quantity--"
                >
                    −
                </button>
                <span aria-live="polite">{{ quantity }}</span>
                <button
                    type="button"
                    :disabled="quantity >= free"
                    aria-label="Больше"
                    @click="quantity++"
                >
                    +
                </button>
            </div>
        </div>

        <div v-if="canBook" class="rb-total">
            <p class="rb-total-line">
                {{ pluralDays(days) }} × {{ formatPrice(price) }}
                <template v-if="quantity > 1"> × {{ quantity }} шт.</template>
            </p>
            <p class="rb-total-sum">{{ formatPrice(total) }}</p>
        </div>

        <button
            type="button"
            class="orange-btn rb-book"
            :disabled="!canBook"
            @click="book"
        >
            Забронировать на эти даты
        </button>

        <p class="rb-secondary">
            <button
                v-if="!addedWithoutDates"
                type="button"
                class="rb-link"
                @click="addWithoutDates"
            >
                Добавить в корзину без дат
            </button>
            <span v-else>
                Добавлено в корзину.
                <a href="/cart" class="rb-link">Перейти в корзину</a>
            </span>
        </p>

        <RentTerms class="rb-terms" />

        <div class="rb-sticky" :class="{ 'rb-sticky-hidden': widgetVisible }">
            <div class="rb-sticky-info">
                <span class="rb-sticky-sum">
                    {{ canBook ? formatPrice(total) : formatPrice(price) }}
                </span>
                <span class="rb-sticky-sub">
                    {{ canBook ? pluralDays(days) : "за сутки" }}
                </span>
            </div>
            <button type="button" class="rb-sticky-btn" @click="stickyAction">
                {{ canBook ? "Забронировать" : "Выбрать даты" }}
            </button>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import cartStore from "../store/cart.js";
import RentPeriodPicker from "../components/RentPeriodPicker.vue";
import RentTerms from "../components/RentTerms.vue";
import {
    copyPeriod,
    formatPrice,
    isPeriodValid,
    pluralDays,
    rentDays,
} from "../utils/rent.js";

export default {
    name: "RentBooking",
    components: { RentPeriodPicker, RentTerms },
    props: {
        goodId: { type: [Number, String], required: true },
        price: { type: Number, required: true },
        basePrice: { type: Number, default: 0 },
    },
    data() {
        return {
            period: copyPeriod(cartStore.getDate(this.goodId)),
            busyDays: [],
            free: null,
            checking: false,
            checkError: false,
            quantity: Math.max(1, cartStore.getQuantity(this.goodId)),
            addedWithoutDates: false,
            widgetVisible: true,
            requestId: 0,
        };
    },
    computed: {
        ready() {
            return isPeriodValid(this.period);
        },
        days() {
            return rentDays(this.period);
        },
        total() {
            return this.days * this.price * this.quantity;
        },
        canBook() {
            return (
                this.ready &&
                !this.checking &&
                !this.checkError &&
                this.free !== null &&
                this.free >= this.quantity &&
                this.quantity > 0
            );
        },
    },
    watch: {
        period: {
            deep: true,
            handler() {
                this.checkAvailability();
            },
        },
    },
    mounted() {
        this.loadBusyDays();
        this.checkAvailability();

        // Липкая панель на телефоне нужна, только пока кнопка бронирования вне экрана.
        if ("IntersectionObserver" in window) {
            const target = this.$el.querySelector(".rb-book");
            this.observer = new IntersectionObserver(([entry]) => {
                this.widgetVisible = entry.isIntersecting;
            });
            this.observer.observe(target);
        }
    },
    beforeUnmount() {
        this.observer?.disconnect();
    },
    methods: {
        formatPrice,
        pluralDays,
        async loadBusyDays() {
            try {
                const response = await axios.get(
                    `/api/good/${this.goodId}/busy-days`,
                    {
                        params: { days: 120 },
                    },
                );
                this.busyDays = response.data.busyDays || [];
            } catch (e) {
                // Подсветка занятых дней — подсказка; без неё виджет работает.
                this.busyDays = [];
            }
        },
        async checkAvailability() {
            this.checkError = false;
            if (!this.ready) {
                this.free = null;
                return;
            }

            // Ответ на устаревший запрос не должен перезаписать результат нового.
            const requestId = ++this.requestId;
            this.checking = true;
            try {
                const response = await axios.post(
                    `/api/get-available-count/${this.goodId}`,
                    {
                        start_date: this.period.dateStart,
                        start_time: this.period.timeStart,
                        end_date: this.period.dateEnd,
                        end_time: this.period.timeEnd,
                    },
                );
                if (requestId !== this.requestId) return;
                this.free = Number(response.data.quantity) || 0;
                if (this.free > 0) {
                    this.quantity = Math.min(
                        Math.max(this.quantity, 1),
                        this.free,
                    );
                }
            } catch (e) {
                if (requestId !== this.requestId) return;
                this.free = null;
                this.checkError = true;
            } finally {
                if (requestId === this.requestId) {
                    this.checking = false;
                }
            }
        },
        book() {
            if (!this.canBook) return;
            cartStore.setItem(
                this.goodId,
                this.quantity,
                copyPeriod(this.period),
            );
            window.location.href = "/cart";
        },
        addWithoutDates() {
            if (!cartStore.getQuantity(this.goodId)) {
                cartStore.addToCart(this.goodId);
            }
            this.addedWithoutDates = true;
        },
        stickyAction() {
            if (this.canBook) {
                this.book();
                return;
            }
            this.$el.scrollIntoView({ behavior: "smooth", block: "start" });
        },
    },
};
</script>

<style scoped>
.rb {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-width: 520px;
}
.rb-price {
    display: flex;
    align-items: baseline;
    gap: 10px;
    flex-wrap: wrap;
}
.rb-price-main {
    font-size: 36px;
    font-weight: 600;
    color: #ffffff;
}
.rb-price-unit {
    font-size: 18px;
    color: #c8c8c8;
}
.rb-price-old {
    font-size: 18px;
    color: #8a8a8a;
}
.rb-status p {
    font-size: 15px;
}
.rb-status-muted {
    color: #8a8a8a;
}
.rb-status-green {
    color: #4cd07d;
}
.rb-status-red {
    color: #ff6b5e;
}
.rb-quantity {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.rb-quantity-label {
    color: #c8c8c8;
}
.rb-stepper {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #191919;
    border-radius: 15px;
    padding: 6px;
    color: #ffffff;
    font-size: 18px;
}
.rb-stepper button {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: none;
    background: #232323;
    color: #ffffff;
    font-size: 20px;
    cursor: pointer;
}
.rb-stepper button:disabled {
    opacity: 0.35;
    cursor: default;
}
.rb-total {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    border-top: 1px solid #2a2a2a;
    padding-top: 14px;
}
.rb-total-line {
    color: #c8c8c8;
    font-size: 15px;
}
.rb-total-sum {
    color: #ffffff;
    font-size: 28px;
    font-weight: 600;
    white-space: nowrap;
}
.rb-book {
    height: 64px;
    font-size: 20px;
}
.rb-book:disabled {
    background: #232323;
    color: #7a7a7a;
    box-shadow: inset 0 0 0 1px var(--line-input, #4a4a4a);
    cursor: not-allowed;
}
.rb-secondary {
    text-align: center;
    font-size: 14px;
    color: #c8c8c8;
}
.rb-link {
    background: none;
    border: none;
    padding: 0;
    color: #ff962e;
    font: inherit;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.rb-terms {
    margin-top: 8px;
}
.rb-stepper button:focus-visible,
.rb-book:focus-visible,
.rb-link:focus-visible,
.rb-sticky-btn:focus-visible {
    outline: 2px solid #ff962e;
    outline-offset: 2px;
}
.rb-sticky {
    display: none;
}
@media (max-width: 992px) {
    .rb {
        max-width: none;
    }
    .rb-sticky {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 100px; /* над нижним меню */
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 15px;
        background: #1f1f1f;
        border-top: 1px solid #2a2a2a;
        transition:
            transform 0.2s ease,
            opacity 0.2s ease;
    }
    .rb-sticky-hidden {
        transform: translateY(20px);
        opacity: 0;
        pointer-events: none;
    }
    .rb-sticky-info {
        display: flex;
        flex-direction: column;
    }
    .rb-sticky-sum {
        color: #ffffff;
        font-size: 20px;
        font-weight: 600;
    }
    .rb-sticky-sub {
        color: #8a8a8a;
        font-size: 13px;
    }
    .rb-sticky-btn {
        height: 48px;
        padding: 0 22px;
        border: none;
        border-radius: 15px;
        background: #ff962e;
        color: #ffffff;
        font: inherit;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }
}
</style>
