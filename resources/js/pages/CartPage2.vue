<template>
    <div>
        <Loader v-if="loadingModal" />

        <p v-if="loading" class="cart-loading">Загружаем корзину…</p>
        <div v-else-if="loadError" class="cart-empty">
            <h4 class="cart-empty-title big-white-title mb-20">
                Не удалось загрузить корзину
            </h4>
            <button
                type="button"
                class="orange-btn cart-empty-btn"
                @click="getCart"
            >
                Попробовать ещё раз
            </button>
        </div>

        <h5 v-if="showCart" class="big-white-title cart-page-title">
            {{ $t("cart.title") }}
        </h5>
        <div v-if="showCart" class="cart-page">
            <div class="cart-list">
                <div
                    v-for="(good, index) in cart"
                    class="cart-item-out"
                    :key="good.id"
                >
                    <div class="cart-item">
                        <div class="cart-item-img">
                            <img
                                :src="
                                    good.attachment && good.attachment.length
                                        ? good.attachment[0].url
                                        : '/img/example.png'
                                "
                                :alt="good.name_ru"
                            />
                        </div>
                        <div class="cart-info">
                            <div class="cart-text">
                                <p class="cart-title white-m-text mb-20">
                                    {{ good.name_ru }}
                                </p>
                                <div class="cart-cost">
                                    <span class="cart-cost-1">
                                        {{
                                            formatPrice(
                                                itemTotal(good) ||
                                                    itemDayCost(good),
                                            )
                                        }}
                                    </span>
                                    <span class="cart-cost-2">
                                        {{ good.quantity }} шт ×
                                        {{
                                            formatPrice(good.client_price)
                                        }}/сутки
                                        <template v-if="itemDays(good)">
                                            × {{ pluralDays(itemDays(good)) }}
                                        </template>
                                    </span>
                                </div>
                            </div>
                            <div class="cart-btns">
                                <div class="cart-btn">
                                    <button
                                        type="button"
                                        class="quantity-btn-minus"
                                        :aria-label="
                                            good.quantity === 1
                                                ? 'Убрать из корзины'
                                                : 'Меньше'
                                        "
                                        @click="minus(index)"
                                    >
                                        <img
                                            v-if="good.quantity === 1"
                                            class="car-delete"
                                            src="/img/delete-white.svg"
                                            alt=""
                                        />
                                        <span v-else>-</span>
                                    </button>
                                    <span class="quantity">{{
                                        good.quantity
                                    }}</span>
                                    <button
                                        type="button"
                                        class="quantity-btn-plus"
                                        aria-label="Больше"
                                        @click="plus(index)"
                                    >
                                        +
                                    </button>
                                </div>
                                <button
                                    type="button"
                                    class="cart-time-btn"
                                    v-if="rentType === 'individual'"
                                    :class="{ filled: periodReady(good.date) }"
                                    :aria-expanded="good.dateOpen"
                                    @click="good.dateOpen = !good.dateOpen"
                                >
                                    <svg
                                        width="19"
                                        height="20"
                                        viewBox="0 0 19 20"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M16.3636 9.09091H1.81818V17.2727C1.81818 17.7748 2.2252 18.1818 2.72727 18.1818H15.4545C15.9566 18.1818 16.3636 17.7748 16.3636 17.2727V9.09091ZM11.8182 4.54545V3.63636H6.36364V4.54545C6.36364 5.04753 5.95662 5.45455 5.45455 5.45455C4.95247 5.45455 4.54545 5.04753 4.54545 4.54545V3.63636H2.72727C2.2252 3.63636 1.81818 4.04338 1.81818 4.54545V7.27273H16.3636V4.54545C16.3636 4.04338 15.9566 3.63636 15.4545 3.63636H13.6364V4.54545C13.6364 5.04753 13.2294 5.45455 12.7273 5.45455C12.2252 5.45455 11.8182 5.04753 11.8182 4.54545ZM18.1818 17.2727C18.1818 18.779 16.9608 20 15.4545 20H2.72727C1.22104 20 0 18.779 0 17.2727V4.54545C0 3.03922 1.22104 1.81818 2.72727 1.81818H4.54545V0.909091C4.54545 0.407014 4.95247 0 5.45455 0C5.95662 0 6.36364 0.407014 6.36364 0.909091V1.81818H11.8182V0.909091C11.8182 0.407014 12.2252 0 12.7273 0C13.2294 0 13.6364 0.407014 13.6364 0.909091V1.81818H15.4545C16.9608 1.81818 18.1818 3.03922 18.1818 4.54545V17.2727Z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                    <span v-if="periodReady(good.date)"
                                        >Сменить даты</span
                                    >
                                    <span v-else>Указать даты</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div
                        class="date-ind"
                        v-if="rentType === 'individual' && good.dateOpen"
                    >
                        <RentPeriodPicker
                            v-model="good.date"
                            @update:modelValue="onItemPeriod(index)"
                        />
                    </div>

                    <div class="cart-item-status" aria-live="polite">
                        <p v-if="good.checking" class="cart-status-text">
                            Проверяем наличие…
                        </p>
                        <p
                            v-else-if="good.checkError"
                            class="cart-status-text status-red"
                        >
                            Не удалось проверить наличие —
                            <button
                                type="button"
                                class="cart-link-btn"
                                @click="getAvailableItems(index)"
                            >
                                повторить
                            </button>
                        </p>
                        <p
                            v-else-if="good.availableItemsQuantity === 0"
                            class="cart-status-text status-red"
                        >
                            На выбранные даты нет свободных экземпляров
                        </p>
                        <p
                            v-else-if="good.availableItems === false"
                            class="cart-status-text status-red"
                        >
                            На выбранные даты свободно только
                            {{ good.availableItemsQuantity }} шт. — уменьшите
                            количество
                        </p>
                        <p
                            v-else-if="good.availableItems === true"
                            class="cart-status-text status-green"
                        >
                            На выбранные даты есть свободные экземпляры
                        </p>
                    </div>

                    <div class="cart-item-additions-out">
                        <p
                            class="cart-additions-title"
                            v-if="good.additions && good.additions.length > 0"
                        >
                            {{ $t("translations.Additional accessories") }}:
                        </p>
                        <div class="cart-item-additions" v-if="good.additions">
                            <div
                                class="cart-item-addition"
                                v-for="(
                                    addition, additionalIndex
                                ) in good.additions"
                                :key="addition.good.id"
                            >
                                <label class="cio-in">
                                    <input
                                        :name="`addition-${good.id}-${addition.good.id}`"
                                        @change="
                                            (event) =>
                                                handleAdditional(
                                                    event,
                                                    index,
                                                    additionalIndex,
                                                )
                                        "
                                        type="checkbox"
                                        class="cia-checkbox"
                                        v-model="addition.added"
                                    />
                                    <div class="cio-before"></div>
                                    <div class="cart-item-addition-checkbox">
                                        <p class="cia-title mb-10">
                                            {{ addition.good.name_ru }}
                                        </p>
                                        <p class="cia-price">
                                            +{{
                                                formatPrice(
                                                    additionPrice(addition),
                                                )
                                            }}/сутки
                                        </p>
                                    </div>
                                </label>
                                <div v-if="addition.added" class="cart-btn">
                                    <button
                                        type="button"
                                        class="quantity-btn-minus"
                                        :aria-label="
                                            addition.quantity === 1
                                                ? 'Убрать дополнение'
                                                : 'Меньше'
                                        "
                                        @click="
                                            minusAdditional(
                                                index,
                                                additionalIndex,
                                            )
                                        "
                                    >
                                        <img
                                            v-if="addition.quantity === 1"
                                            class="car-delete"
                                            src="/img/delete-white.svg"
                                            alt=""
                                        />
                                        <span v-else>-</span>
                                    </button>
                                    <span class="quantity">{{
                                        addition.quantity
                                    }}</span>
                                    <button
                                        type="button"
                                        class="quantity-btn-plus"
                                        aria-label="Больше"
                                        :disabled="
                                            addition.availableQuantity !==
                                                null &&
                                            addition.quantity >=
                                                addition.availableQuantity
                                        "
                                        @click="
                                            plusAdditional(
                                                index,
                                                additionalIndex,
                                            )
                                        "
                                    >
                                        +
                                    </button>
                                </div>

                                <p
                                    v-if="
                                        addition.added &&
                                        addition.availableQuantity !== null &&
                                        addition.availableQuantity <
                                            addition.quantity
                                    "
                                    class="cart-status-text status-red"
                                >
                                    На выбранные даты свободно:
                                    {{ addition.availableQuantity }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="cart-actions" ref="actions">
                <div
                    v-if="clientLoaded && !isAuthenticated"
                    class="cart-guest-note"
                >
                    Чтобы оформить заказ, нужно
                    <a href="/auth/login">войти</a> или
                    <a href="/auth/register">создать аккаунт</a> — корзина
                    сохранится.
                </div>

                <p class="white-m-text mb-20">Даты аренды</p>
                <div
                    class="select-time mb-20"
                    role="radiogroup"
                    aria-label="Даты аренды"
                >
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="rentType === 'all'"
                        :class="{
                            'select-time-option': true,
                            'selected-option': rentType === 'all',
                        }"
                        @click="setRentType('all')"
                    >
                        <span class="option-circle">
                            <span class="option-circle-in"></span>
                        </span>
                        <span class="option-text">Для всех товаров</span>
                    </button>
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="rentType === 'individual'"
                        :class="{
                            'select-time-option': true,
                            'selected-option': rentType === 'individual',
                        }"
                        @click="setRentType('individual')"
                    >
                        <span class="option-circle">
                            <span class="option-circle-in"></span>
                        </span>
                        <span class="option-text">Для каждого свои</span>
                    </button>
                </div>
                <div class="date-all mb-20" v-if="rentType === 'all'">
                    <RentPeriodPicker
                        v-model="date"
                        @update:modelValue="onAllPeriod"
                    />
                </div>

                <div class="cart-summary" ref="summary">
                    <template v-if="allPeriodsReady">
                        <div class="cart-summary-row" v-if="rentType === 'all'">
                            <span>Аренда</span>
                            <span>{{ pluralDays(itemDays(cart[0])) }}</span>
                        </div>
                        <div class="cart-summary-row">
                            <span>Техника</span>
                            <span>{{ formatPrice(goodsSum) }}</span>
                        </div>
                        <div class="cart-summary-row" v-if="additionsSum > 0">
                            <span>Доп. аксессуары</span>
                            <span>{{ formatPrice(additionsSum) }}</span>
                        </div>
                        <div class="cart-summary-total">
                            <span>Итого</span>
                            <span class="cart-sum-price">{{
                                formatPrice(sumPrice)
                            }}</span>
                        </div>
                        <p v-if="bonusAmount > 0" class="cart-summary-bonus">
                            + {{ formatPrice(bonusAmount) }} вернётся бонусами
                            после возврата техники
                        </p>
                    </template>
                    <template v-else>
                        <div class="cart-summary-total">
                            <span>За сутки</span>
                            <span class="cart-sum-price">{{
                                formatPrice(dayPrice)
                            }}</span>
                        </div>
                        <p class="cart-summary-hint">
                            Выберите даты — посчитаем итог за весь срок
                        </p>
                    </template>
                </div>

                <div class="cart-end-block">
                    <p class="cart-end-mobile-total">
                        <span>{{
                            allPeriodsReady ? "Итого" : "За сутки"
                        }}</span>
                        <b>{{
                            formatPrice(allPeriodsReady ? sumPrice : dayPrice)
                        }}</b>
                    </p>
                    <div class="cart-end-2 mb-20">
                        <p class="cart-end-count">
                            {{ pluralGoods(totalQuantity) }}
                        </p>
                        <button
                            type="button"
                            class="cart-clear-btn"
                            @click="clearCart"
                        >
                            Очистить корзину
                        </button>
                    </div>
                    <button
                        type="button"
                        @click="modalOrder"
                        :disabled="!endAvailable"
                        :class="!endAvailable ? 'end-disabled' : ''"
                        class="orange-btn end-cart-btn"
                    >
                        Перейти к оформлению
                    </button>
                    <p v-if="!endAvailable" class="end-warning-text">
                        {{ blockReason }}
                    </p>
                </div>

                <RentTerms class="cart-terms" />
            </div>
        </div>

        <!-- Empty cart texts -->
        <div v-if="!loading && !loadError && isCartEmpty" class="cart-empty">
            <svg
                class="cart-empty-icon mb-20"
                width="40"
                height="40"
                viewBox="0 0 40 40"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                aria-hidden="true"
            >
                <path
                    d="M36.3636 20C36.3636 10.9626 29.0374 3.63636 20 3.63636C10.9626 3.63636 3.63636 10.9626 3.63636 20C3.63636 29.0374 10.9626 36.3636 20 36.3636C29.0374 36.3636 36.3636 29.0374 36.3636 20ZM14.1779 28.3683H14.1797V28.3665L14.1815 28.3647L14.1797 28.3629C13.5772 29.1657 12.4402 29.329 11.6371 28.7269C10.8338 28.1245 10.6708 26.9858 11.2731 26.1825L12.1431 26.8342C11.2938 26.1969 11.2732 26.1816 11.2731 26.1808L11.2766 26.1772C11.2781 26.1752 11.2801 26.1725 11.282 26.1701C11.2856 26.1653 11.2894 26.1589 11.2944 26.1523C11.3045 26.1392 11.3178 26.1224 11.3335 26.1026C11.3648 26.0631 11.4081 26.0115 11.4613 25.9482C11.5679 25.8211 11.7187 25.6476 11.9123 25.4457C12.2979 25.0434 12.8617 24.5142 13.5902 23.9844C15.041 22.9293 17.2264 21.8182 20 21.8182C22.7736 21.8182 24.959 22.9293 26.4098 23.9844C27.1383 24.5142 27.7021 25.0434 28.0877 25.4457C28.2813 25.6476 28.4321 25.8211 28.5387 25.9482C28.5919 26.0115 28.6352 26.0631 28.6665 26.1026C28.6822 26.1224 28.6955 26.1392 28.7056 26.1523C28.7106 26.1589 28.7144 26.1653 28.718 26.1701C28.7199 26.1725 28.7219 26.1752 28.7234 26.1772L28.7269 26.1808C28.7268 26.1816 28.7062 26.1969 27.8569 26.8342L28.7269 26.1825C29.3292 26.9858 29.1662 28.1245 28.3629 28.7269C27.5596 29.3292 26.421 29.1662 25.8185 28.3629V28.3647L25.8203 28.3665V28.3683L25.815 28.3612C25.8042 28.3475 25.7847 28.3206 25.7546 28.2848C25.6944 28.2131 25.5962 28.102 25.4634 27.9634C25.1956 27.684 24.793 27.3036 24.272 26.9247C23.2228 26.1617 21.7718 25.4545 20 25.4545C18.2282 25.4545 16.7772 26.1617 15.728 26.9247C15.207 27.3036 14.8044 27.684 14.5366 27.9634C14.4038 28.102 14.3056 28.2131 14.2454 28.2848C14.2153 28.3206 14.1958 28.3475 14.185 28.3612C14.1824 28.3645 14.1794 28.3664 14.1779 28.3683ZM14.5632 12.7273L14.7496 12.7362C15.6664 12.8293 16.3814 13.6041 16.3814 14.5455C16.3814 15.4868 15.6664 16.2616 14.7496 16.3548L14.5632 16.3636H14.5455C13.5413 16.3636 12.7273 15.5496 12.7273 14.5455C12.7273 13.5413 13.5413 12.7273 14.5455 12.7273H14.5632ZM25.4723 12.7273L25.6587 12.7362C26.5755 12.8293 27.2905 13.6041 27.2905 14.5455C27.2905 15.4868 26.5755 16.2616 25.6587 16.3548L25.4723 16.3636H25.4545C24.4504 16.3636 23.6364 15.5496 23.6364 14.5455C23.6364 13.5413 24.4504 12.7273 25.4545 12.7273H25.4723ZM40 20C40 31.0457 31.0457 40 20 40C8.9543 40 0 31.0457 0 20C0 8.9543 8.9543 0 20 0C31.0457 0 40 8.9543 40 20Z"
                    fill="white"
                />
            </svg>
            <h4 class="cart-empty-title big-white-title mb-20">
                Корзина пуста
            </h4>
            <p class="cart-empty-text mb-20">
                Воспользуйтесь каталогом или поиском, <br />
                чтобы найти всё, что нужно
            </p>
            <a href="/categories" class="orange-btn cart-empty-btn"
                >Перейти в каталог</a
            >
        </div>
    </div>

    <div
        v-if="modal.info.open"
        @click="modalCloseInfo()"
        class="modal-overlay"
        style="z-index: 1002; display: block; opacity: 0.5"
    ></div>
    <div
        v-if="modal.info.open"
        id="modal-info"
        class="modal"
        role="dialog"
        aria-modal="true"
        style="
            z-index: 1003;
            display: block;
            opacity: 1;
            bottom: 0;
            transform: scaleX(1) scaleY(1);
        "
    >
        <div class="black-block simple-centred-block modal-block">
            <button
                type="button"
                class="close"
                aria-label="Закрыть"
                @click="modalCloseInfo()"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 16 16"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z"
                        fill="#8a8a8a"
                    />
                    <path
                        d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z"
                        fill="#8a8a8a"
                    />
                </svg>
            </button>
            <p class="modal-title big-white-title mb-20">
                <b>{{ $t("translations.IMPORTANT!") }}</b>
            </p>
            <p class="grey-s-light-text mb-20">
                {{
                    $t(
                        "translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract",
                    )
                }}
            </p>
            <p class="grey-s-light-text mb-20">
                {{
                    $t(
                        "translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays",
                    )
                }}
            </p>
        </div>
    </div>

    <div
        v-if="modal.open && !isAuthenticated"
        style="
            z-index: 1003;
            display: block;
            opacity: 1;
            bottom: 0;
            transform: scaleX(1) scaleY(1);
        "
        id="modal-guest"
        class="modal"
        role="dialog"
        aria-modal="true"
    >
        <div class="black-block simple-centred-block modal-block">
            <button
                type="button"
                class="close"
                aria-label="Закрыть"
                @click="modalClose()"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 16 16"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z"
                        fill="#8a8a8a"
                    />
                    <path
                        d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z"
                        fill="#8a8a8a"
                    />
                </svg>
            </button>
            <p class="modal-title big-white-title mb-20">Войдите в аккаунт</p>
            <p class="grey-s-light-text mb-20">
                Для того, чтобы создать заявку на аренду – Войдите или Создайте
                аккаунт. Корзина и выбранные даты сохранятся.
            </p>
            <a href="/auth/login" class="orange-btn mb-20">Войти</a>
            <a href="/auth/register" class="black-btn">Создать аккаунт</a>
        </div>
    </div>
    <div
        v-if="modal.open"
        @click="modalClose()"
        class="modal-overlay"
        style="z-index: 1002; display: block; opacity: 0.5"
    ></div>
    <div
        v-if="modal.open && isAuthenticated"
        id="modal-order"
        class="modal"
        role="dialog"
        aria-modal="true"
        style="
            z-index: 1003;
            display: block;
            opacity: 1;
            bottom: 0;
            transform: scaleX(1) scaleY(1);
        "
    >
        <div class="black-block simple-centred-block modal-block">
            <button
                type="button"
                class="close"
                aria-label="Закрыть"
                @click="modalClose()"
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 16 16"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z"
                        fill="#8a8a8a"
                    />
                    <path
                        d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z"
                        fill="#8a8a8a"
                    />
                </svg>
            </button>
            <p class="modal-title big-white-title mb-20">Подтвердите заказ</p>
            <p class="cart-modal-total mb-20">
                Итого: <b>{{ formatPrice(sumPrice) }}</b>
            </p>
            <div
                class="select-time mb-20"
                role="radiogroup"
                aria-label="Способ получения"
            >
                <button
                    type="button"
                    role="radio"
                    :aria-checked="!isDelivery"
                    :class="{
                        'select-time-option': true,
                        'selected-option': !isDelivery,
                    }"
                    @click="isDelivery = false"
                >
                    <span class="option-circle">
                        <span class="option-circle-in"></span>
                    </span>
                    <span class="option-text">Самовывоз</span>
                </button>
                <button
                    type="button"
                    role="radio"
                    :aria-checked="isDelivery"
                    :class="{
                        'select-time-option': true,
                        'selected-option': isDelivery,
                    }"
                    @click="isDelivery = true"
                >
                    <span class="option-circle">
                        <span class="option-circle-in"></span>
                    </span>
                    <span class="option-text">Доставка</span>
                </button>
            </div>

            <div v-if="isDelivery" class="mb-20 delivery-inputs">
                <p class="grey-s-light-text mb-20">
                    После подтверждения оформления заказа ваш заказ будет
                    передан менеджеру для обработки. Доставка будет выполнена по
                    указанному адресу после подтверждения деталей с вами.
                </p>
                <BaseInput
                    v-model="delivery.street"
                    class="delivery-input"
                    placeholder="Улица/Микрорайон *"
                    :error="
                        deliveryTouched && !delivery.street
                            ? 'Обязательное поле'
                            : ''
                    "
                />
                <BaseInput
                    v-model="delivery.house"
                    class="delivery-input"
                    placeholder="Дом *"
                    :error="
                        deliveryTouched && !delivery.house
                            ? 'Обязательное поле'
                            : ''
                    "
                />
                <BaseInput
                    v-model="delivery.apartment"
                    class="delivery-input"
                    placeholder="Квартира"
                />
                <BaseInput
                    v-model="delivery.entrance"
                    class="delivery-input"
                    placeholder="Подъезд"
                />
                <BaseInput
                    v-model="delivery.floor"
                    class="delivery-input"
                    placeholder="Этаж"
                />
                <BaseInput
                    v-model="delivery.comment"
                    class="delivery-input"
                    placeholder="Комментарий"
                />
            </div>
            <div v-if="!isDelivery">
                <p class="grey-s-light-text mb-20">
                    {{
                        $t(
                            "translations.After confirming your order, your order will be transferred to the manager and you will be able to receive your goods at the pick-up point at",
                        ) + ":"
                    }}
                </p>
                <a
                    :href="pickupPoint.mapUrl"
                    target="_blank"
                    rel="noopener"
                    class="cart-address"
                >
                    <svg
                        width="13"
                        height="16"
                        viewBox="0 0 13 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M11.5492 6.29332C11.4873 5.03493 10.9641 3.84045 10.0751 2.94531C9.18622 2.05018 8.00004 1.52329 6.75038 1.46094L6.5 1.45455C5.15918 1.45455 3.87296 1.99058 2.92486 2.94531C1.97676 3.90004 1.44444 5.19526 1.44444 6.54545C1.44444 8.11587 2.32324 9.80882 3.46512 11.3196C4.56853 12.7794 5.83523 13.9609 6.5 14.5419C7.16477 13.9609 8.43147 12.7794 9.53488 11.3196C10.6768 9.80882 11.5556 8.11587 11.5556 6.54545L11.5492 6.29332ZM7.94444 6.54545C7.94444 5.74213 7.29774 5.09091 6.5 5.09091C5.70226 5.09091 5.05556 5.74213 5.05556 6.54545C5.05556 7.34878 5.70226 8 6.5 8C7.29774 8 7.94444 7.34878 7.94444 6.54545ZM9.38889 6.54545C9.38889 8.1521 8.09549 9.45455 6.5 9.45455C4.90451 9.45455 3.61111 8.1521 3.61111 6.54545C3.61111 4.93881 4.90451 3.63636 6.5 3.63636C8.09549 3.63636 9.38889 4.93881 9.38889 6.54545ZM13 6.54545C13 8.60631 11.8787 10.6203 10.6845 12.2003C9.62502 13.6021 8.43786 14.7534 7.69548 15.4212L7.4063 15.6768C7.394 15.6875 7.38121 15.6983 7.36822 15.7081C7.14927 15.8739 6.88853 15.9728 6.61708 15.995L6.5 16C6.22606 16 5.95864 15.9214 5.72841 15.7749L5.63178 15.7081L5.5937 15.6768C4.90377 15.077 3.52624 13.8022 2.31548 12.2003C1.12134 10.6203 0 8.60631 0 6.54545C0 4.80949 0.684605 3.14441 1.90359 1.9169C3.12258 0.689393 4.77609 0 6.5 0C8.22391 0 9.87742 0.689393 11.0964 1.9169C12.3154 3.14441 13 4.80949 13 6.54545Z"
                            fill="#FF962E"
                        />
                    </svg>
                    {{ pickupPoint.address }}</a
                >
                <p class="grey-s-light-text mb-20 cart-pickup-hours">
                    {{ pickupPoint.hours }}
                </p>
            </div>

            <p class="grey-s-light-text mb-20">
                {{
                    $t(
                        "translations.The manager will double-check your contact details and photographs of your ID, after which an equipment rental agreement will be signed",
                    ) + "."
                }}
            </p>
            <p v-if="orderError" class="cart-order-error mb-20" role="alert">
                {{ orderError }}
            </p>
            <button
                type="button"
                class="orange-btn mb-20"
                :class="{ 'order-disabled': !canPlaceOrder }"
                :disabled="loadingModal"
                @click="orderEvent()"
            >
                {{ $t("translations.Place order") }}
            </button>
            <div class="cart-agree">
                <label class="cio-in" style="margin: 0">
                    <input
                        name="checkbox-agree"
                        type="checkbox"
                        class="cia-checkbox"
                        v-model="agree"
                    />
                    <div class="cio-before"></div>
                </label>
                <span class="grey-s-text">Я согласен с </span>
                <button
                    type="button"
                    @click="modalOpenInfo()"
                    class="orange-s-link cart-link-btn"
                >
                    Условиями аренды
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";

import cartStore from "../store/cart.js";
import BaseInput from "../components/BaseInput.vue";
import Loader from "../components/Loader.vue";
import RentPeriodPicker from "../components/RentPeriodPicker.vue";
import RentTerms from "../components/RentTerms.vue";
import {
    copyPeriod,
    formatPrice,
    isPeriodComplete,
    isPeriodValid,
    pluralDays,
    pluralGoods,
    rentDays,
    samePeriod,
} from "../utils/rent.js";

export default {
    components: { Loader, BaseInput, RentPeriodPicker, RentTerms },
    data() {
        return {
            cart: [],
            loading: true,
            loadError: false,
            isCartEmpty: false,
            rentType: "all",
            date: copyPeriod(null),
            isAuthenticated: false,
            clientLoaded: false,
            clientData: null,
            modal: {
                info: {
                    open: false,
                },
                open: false,
            },
            agree: false,
            loadingModal: false,
            orderError: "",
            isDelivery: false,
            deliveryTouched: false,
            delivery: {
                street: null,
                house: null,
                apartment: null,
                entrance: null,
                floor: null,
                comment: null,
            },
        };
    },
    computed: {
        showCart() {
            return !this.loading && !this.loadError && !this.isCartEmpty;
        },
        pickupPoint() {
            const info = window.PIXEL_INFO;
            return info ? info.pickup : { address: "", hours: "", mapUrl: "#" };
        },
        totalQuantity() {
            return this.cart.reduce((sum, good) => sum + good.quantity, 0);
        },
        dayPrice() {
            return this.cart.reduce(
                (sum, good) => sum + this.itemDayCost(good),
                0,
            );
        },
        allPeriodsReady() {
            return (
                this.cart.length > 0 &&
                this.cart.every((good) => isPeriodValid(this.periodOf(good)))
            );
        },
        goodsSum() {
            return this.cart.reduce(
                (sum, good) =>
                    sum +
                    this.itemDays(good) * good.client_price * good.quantity,
                0,
            );
        },
        additionsSum() {
            return this.cart.reduce(
                (sum, good) =>
                    sum + this.itemDays(good) * this.itemAdditionsDayCost(good),
                0,
            );
        },
        sumPrice() {
            return this.goodsSum + this.additionsSum;
        },
        bonusAmount() {
            const percent = this.clientData?.bonusAccrualPercent || 0;
            return this.allPeriodsReady
                ? Math.floor((this.sumPrice * percent) / 100)
                : 0;
        },
        blockReason() {
            if (!this.allPeriodsReady) {
                return this.rentType === "all"
                    ? "Выберите даты и время аренды"
                    : "Укажите даты для каждого товара";
            }
            if (this.cart.some((good) => good.checking)) {
                return "Проверяем наличие…";
            }
            const busy = this.cart.filter(
                (good) => good.availableItems !== true,
            );
            if (busy.length) {
                return (
                    "Нет свободных экземпляров: " +
                    busy.map((g) => g.name_ru).join(", ")
                );
            }
            return "Не хватает дополнительных аксессуаров на эти даты";
        },
        endAvailable() {
            if (!this.allPeriodsReady) return false;
            return this.cart.every(
                (good) =>
                    !good.checking &&
                    good.availableItems === true &&
                    good.additions.every(
                        (addition) =>
                            !addition.added ||
                            addition.availableQuantity === null ||
                            addition.availableQuantity >= addition.quantity,
                    ),
            );
        },
        canPlaceOrder() {
            if (!this.agree) return false;
            if (
                this.isDelivery &&
                (!this.delivery.street || !this.delivery.house)
            ) {
                return false;
            }
            return this.endAvailable;
        },
    },
    watch: {
        isDelivery() {
            this.orderError = "";
        },
    },
    mounted() {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        if (csrfToken) {
            axios.defaults.headers.common["X-CSRF-TOKEN"] = csrfToken;
        }
        this.getCart();
        this.getClientData();
    },
    methods: {
        formatPrice,
        pluralDays,
        pluralGoods,
        periodReady(period) {
            return isPeriodValid(period);
        },
        periodOf(good) {
            return this.rentType === "all" ? this.date : good.date;
        },
        itemDays(good) {
            return good ? rentDays(this.periodOf(good)) : 0;
        },
        additionPrice(addition) {
            const good = addition.good;
            if (good.client_additional_price !== undefined) {
                return good.client_additional_price;
            }
            return good.additional_cost > 0 ? good.additional_cost : good.cost;
        },
        itemAdditionsDayCost(good) {
            return good.additions.reduce(
                (sum, addition) =>
                    addition.added
                        ? sum + this.additionPrice(addition) * addition.quantity
                        : sum,
                0,
            );
        },
        itemDayCost(good) {
            return (
                good.client_price * good.quantity +
                this.itemAdditionsDayCost(good)
            );
        },
        itemTotal(good) {
            return this.itemDays(good) * this.itemDayCost(good);
        },

        /**
         * Получить корзину
         */
        async getCart() {
            this.loading = true;
            this.loadError = false;
            const stored = cartStore.getCart();
            const cartItems = Object.keys(stored);

            if (cartItems.length === 0) {
                this.cart = [];
                this.isCartEmpty = true;
                this.loading = false;
                return;
            }

            let items;
            try {
                const response = await axios.post("/api/cart/items", {
                    items: cartItems,
                });
                items = response.data.items;
            } catch (e) {
                this.loadError = true;
                this.loading = false;
                return;
            }

            items.forEach((item) => {
                item.quantity = cartStore.getQuantity(item.id);
                item.availableItems = null;
                item.availableItemsQuantity = null;
                item.checking = false;
                item.checkError = false;
                item.requestId = 0;
                item.additions = [];
                item.date = copyPeriod(cartStore.getDate(item.id));
                item.dateOpen = false;
            });

            this.cart = items;
            this.isCartEmpty = items.length === 0;
            this.restorePeriods();
            this.loading = false;
        },

        /**
         * Даты, выбранные на странице товара или в прошлый раз в корзине.
         * Одинаковые у всех товаров → режим «для всех», иначе — у каждого свои.
         */
        restorePeriods() {
            const withDates = this.cart.filter((good) =>
                isPeriodComplete(good.date),
            );
            if (withDates.length === 0) {
                return;
            }

            // Частый случай: один товар забронировали со страницы товара с датами,
            // остальные добавили без дат — тогда эти даты разумно применить ко всем.
            const first = withDates[0].date;
            const allSame = withDates.every((good) =>
                samePeriod(good.date, first),
            );

            if (allSame || this.cart.length === 1) {
                this.rentType = "all";
                this.date = copyPeriod(first);
                this.onAllPeriod();
            } else {
                this.rentType = "individual";
                this.cart.forEach((good, index) => {
                    good.dateOpen = !isPeriodComplete(good.date);
                    if (isPeriodValid(good.date)) {
                        this.refreshItem(index);
                    }
                });
            }
        },

        async getAvailableItems(index) {
            const good = this.cart[index];
            const period = this.periodOf(good);
            if (!isPeriodValid(period)) {
                good.availableItems = null;
                good.availableItemsQuantity = null;
                return;
            }

            // Ответ на устаревший запрос (даты успели поменять) не должен перезаписать новый.
            const requestId = ++good.requestId;
            good.checking = true;
            good.checkError = false;
            try {
                const response = await axios.post(
                    `/api/get-available-count/${good.id}`,
                    {
                        end_date: period.dateEnd,
                        end_time: period.timeEnd,
                        start_date: period.dateStart,
                        start_time: period.timeStart,
                    },
                );
                if (requestId !== good.requestId) return;
                good.availableItemsQuantity =
                    Number(response.data.quantity) || 0;
                good.availableItems =
                    good.availableItemsQuantity >= good.quantity;
            } catch (e) {
                if (requestId !== good.requestId) return;
                good.checkError = true;
                good.availableItems = null;
            } finally {
                if (requestId === good.requestId) {
                    good.checking = false;
                }
            }
        },
        async getAvailableItemsAddition(index, additionalIndex) {
            const good = this.cart[index];
            const addition = good.additions[additionalIndex];
            const period = this.periodOf(good);
            try {
                const response = await axios.post(
                    `/api/get-available-count/${addition.good.id}`,
                    {
                        end_date: period.dateEnd,
                        end_time: period.timeEnd,
                        start_date: period.dateStart,
                        start_time: period.timeStart,
                    },
                );
                addition.availableQuantity =
                    Number(response.data.quantity) || 0;
            } catch (e) {
                addition.availableQuantity = null;
            }
        },

        async getAvailableAdditions(index) {
            const good = this.cart[index];
            const period = this.periodOf(good);
            let additions = [];
            try {
                const response = await axios.post(
                    "/api/get-available-additions",
                    {
                        endDate: period.dateEnd,
                        endTime: period.timeEnd,
                        startDate: period.dateStart,
                        startTime: period.timeStart,
                        goodId: good.id,
                    },
                );
                additions = response.data.additionals || [];
            } catch (e) {
                return;
            }

            // Смена дат не должна сбрасывать уже отмеченные дополнения.
            const previous = new Map(good.additions.map((a) => [a.good.id, a]));
            additions.forEach((addition) => {
                const old = previous.get(addition.good.id);
                addition.added = old ? old.added : false;
                addition.quantity = old ? old.quantity : 0;
                addition.availableQuantity = null;
            });
            good.additions = additions;

            good.additions.forEach((addition, additionIndex) => {
                this.getAvailableItemsAddition(index, additionIndex);
            });
        },
        async getClientData() {
            try {
                const response = await axios.get("/api/client", {
                    withCredentials: true,
                });
                this.clientData = response.data.clientData;
                this.isAuthenticated = response.data.isAuthenticated;
            } catch (e) {
                this.isAuthenticated = false;
            } finally {
                this.clientLoaded = true;
            }
        },
        refreshItem(index) {
            this.getAvailableItems(index);
            this.getAvailableAdditions(index);
        },
        onAllPeriod() {
            // Копия на каждый товар: один общий объект привязал бы даты всех товаров друг к другу.
            this.cart.forEach((good, index) => {
                good.date = copyPeriod(this.date);
                cartStore.setDate(
                    good.id,
                    isPeriodComplete(this.date) ? copyPeriod(this.date) : null,
                );
                if (isPeriodValid(this.date)) {
                    this.refreshItem(index);
                } else {
                    good.availableItems = null;
                    good.availableItemsQuantity = null;
                }
            });
        },
        onItemPeriod(index) {
            const good = this.cart[index];
            cartStore.setDate(
                good.id,
                isPeriodComplete(good.date) ? copyPeriod(good.date) : null,
            );
            if (isPeriodValid(good.date)) {
                this.refreshItem(index);
            } else {
                good.availableItems = null;
                good.availableItemsQuantity = null;
            }
        },
        setRentType(type) {
            if (this.rentType === type) return;
            this.rentType = type;
            if (type === "all") {
                this.onAllPeriod();
            } else {
                // Переходя к «своим датам», каждый товар стартует с общего периода.
                this.cart.forEach((good, index) => {
                    good.date = copyPeriod(this.date);
                    good.dateOpen = !isPeriodValid(good.date);
                    this.onItemPeriod(index);
                });
            }
        },
        minus(index) {
            const good = this.cart[index];
            if (good.quantity === 1) {
                cartStore.removeFromCart(good.id);
                this.cart.splice(index, 1);
                this.isCartEmpty = this.cart.length === 0;
                return;
            }
            good.quantity--;
            cartStore.decreaseQuantity(good.id);
            this.recheckQuantity(good);
        },
        plus(index) {
            const good = this.cart[index];
            good.quantity++;
            cartStore.addToCart(good.id);
            this.recheckQuantity(good);
        },
        recheckQuantity(good) {
            // Количество свободных уже известно — повторный запрос не нужен.
            if (good.availableItemsQuantity !== null) {
                good.availableItems =
                    good.availableItemsQuantity >= good.quantity;
            }
        },
        minusAdditional(index, additionalIndex) {
            const addition = this.cart[index].additions[additionalIndex];
            if (addition.quantity === 1) {
                this.removeAddition(index, additionalIndex);
                return;
            }
            addition.quantity--;
        },
        plusAdditional(index, additionalIndex) {
            this.cart[index].additions[additionalIndex].quantity++;
        },
        handleAdditional(event, index, additionalIndex) {
            if (event.target.checked) {
                this.addAddition(index, additionalIndex);
            } else {
                this.removeAddition(index, additionalIndex);
            }
        },
        addAddition(index, additionIndex) {
            const addition = this.cart[index].additions[additionIndex];
            addition.added = true;
            addition.quantity = 1;
        },
        removeAddition(index, additionIndex) {
            const addition = this.cart[index].additions[additionIndex];
            addition.added = false;
            addition.quantity = 0;
        },
        modalOrder() {
            if (!this.endAvailable) {
                return;
            }
            this.orderError = "";
            this.modal.open = true;
            document.body.style.overflow = "hidden";
        },
        modalClose() {
            this.modal.open = false;
            document.body.style.overflow = "";
        },
        async orderEvent() {
            this.orderError = "";
            if (this.isDelivery) {
                this.deliveryTouched = true;
            }
            if (!this.canPlaceOrder) {
                if (!this.agree) {
                    this.orderError = "Отметьте согласие с условиями аренды";
                }
                return;
            }

            const data = this.cart.map((good) => ({
                id: good.id,
                quantity: good.quantity,
                date: copyPeriod(this.periodOf(good)),
                additions: good.additions
                    .filter(
                        (addition) => addition.added && addition.quantity > 0,
                    )
                    .map((addition) => ({
                        goodId: addition.good.id,
                        quantity: addition.quantity,
                    })),
            }));

            this.loadingModal = true;
            let response;
            try {
                response = await axios.post("/api/order", {
                    cart: data,
                    is_delivery: this.isDelivery,
                    delivery: this.delivery,
                });
            } catch (e) {
                this.loadingModal = false;
                if (e.response?.status === 422) {
                    const errors = e.response.data?.errors || {};
                    const first = Object.values(errors)[0];
                    this.orderError = Array.isArray(first)
                        ? first[0]
                        : "Проверьте даты и данные заказа";
                } else if (e.response?.status === 419) {
                    this.orderError =
                        "Сессия устарела — обновите страницу и попробуйте снова";
                } else {
                    this.orderError =
                        "Не удалось оформить заказ. Проверьте интернет и попробуйте ещё раз";
                }
                return;
            }

            if (response.data.success === true) {
                cartStore.clearCart();
                window.location.href = "/profile/orders?new_order=1";
                return;
            }

            this.loadingModal = false;
            if (response.data.status === "spamGuard") {
                window.location.href = "/order/try-again-later";
            } else if (response.data.status === "notAvailable") {
                this.orderError =
                    response.data.message ||
                    "Часть техники уже заняли — выберите другие даты";
                // Даты могли занять, пока клиент оформлял, — обновим статусы в корзине.
                this.cart.forEach((good, index) =>
                    this.getAvailableItems(index),
                );
            } else {
                this.orderError =
                    response.data.message ||
                    "Что-то пошло не так, попробуйте ещё раз";
            }
        },
        clearCart() {
            if (!window.confirm("Удалить все товары из корзины?")) {
                return;
            }
            cartStore.clearCart();
            this.cart = [];
            this.isCartEmpty = true;
        },
        modalCloseInfo() {
            this.modal.info.open = false;
            document.body.style.overflow = "";
            this.modalOrder();
        },
        modalOpenInfo() {
            this.modal.info.open = true;
            document.body.style.overflow = "hidden";
            this.modal.open = false;
        },
    },
};
</script>

<style>
@media (max-width: 992px) {
    .search-block {
        display: none;
    }
    .content {
        padding-top: 0;
    }
    /* Место под зафиксированный блок оформления и нижнее меню */
    footer {
        padding-bottom: 300px !important;
    }
}
</style>

<style scoped>
.cart-list,
.cart-actions {
    min-width: 0;
}
.cart-item-status .cart-status-text {
    color: #8a8a8a;
}
.cart-item-status .status-red {
    color: #ff6b5e;
}
.cart-item-status .status-green {
    color: #5dd4a2;
}
.cart-loading {
    color: #8a8a8a;
    margin-top: 40px;
}
.cart-guest-note {
    background: #191919;
    border: 1px solid #2e2e2e;
    border-radius: 15px;
    padding: 14px 16px;
    margin-bottom: 20px;
    color: #c8c8c8;
    font-size: 14px;
    line-height: 1.5;
}
.cart-guest-note a {
    color: #ff962e;
}
.select-time-option {
    border: none;
    font: inherit;
    text-align: left;
    cursor: pointer;
}
.quantity-btn-minus,
.quantity-btn-plus {
    background: none;
    border: none;
    color: inherit;
    font: inherit;
    cursor: pointer;
}
.quantity-btn-plus:disabled {
    opacity: 0.35;
    cursor: default;
}
.cart-time-btn {
    border: none;
    font: inherit;
}
.date-ind {
    display: block;
    max-width: 480px;
    margin-bottom: 16px;
}
.cart-summary {
    background: #191919;
    border-radius: 20px;
    padding: 18px 20px;
    margin-bottom: 20px;
}
.cart-summary-row {
    display: flex;
    justify-content: space-between;
    color: #c8c8c8;
    font-size: 15px;
    margin-bottom: 8px;
}
.cart-summary-total {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    color: #ffffff;
    font-size: 18px;
    border-top: 1px solid #2a2a2a;
    padding-top: 12px;
    margin-top: 4px;
}
.cart-summary-total:first-child {
    border-top: none;
    padding-top: 0;
    margin-top: 0;
}
.cart-summary-total .cart-sum-price {
    margin: 0;
    font-size: 32px;
    font-weight: 600;
}
.cart-summary-bonus {
    margin-top: 10px;
    font-size: 14px;
    color: #4cd07d;
}
.cart-summary-hint {
    margin-top: 8px;
    font-size: 14px;
    color: #8a8a8a;
}
.cart-end-count {
    color: #8a8a8a;
}
/* Очистка корзины — второстепенное действие, а не яркая кнопка рядом с главной */
.cart-clear-btn {
    background: none;
    border: none;
    padding: 0;
    color: #8a8a8a;
    font: inherit;
    font-size: 14px;
    text-decoration: underline;
    text-underline-offset: 3px;
    cursor: pointer;
}
.cart-clear-btn:hover {
    color: #ff6b5e;
}
.end-cart-btn:disabled {
    cursor: default;
}
.end-warning-text {
    color: #8a8a8a;
    font-size: 13px;
}
.cart-terms {
    margin-top: 24px;
}
.cart-link-btn {
    background: none;
    border: none;
    padding: 0;
    color: #ff962e;
    font: inherit;
    cursor: pointer;
    text-decoration: underline;
}
.cart-order-error {
    color: #ff6b5e;
    font-size: 15px;
}
.cart-modal-total {
    color: #c8c8c8;
    font-size: 18px;
}
.cart-modal-total b {
    color: #ffffff;
}
.cart-pickup-hours {
    margin-top: 6px;
}
.close {
    background: none;
    border: none;
    cursor: pointer;
}
button:focus-visible {
    outline: 2px solid #ff962e;
    outline-offset: 2px;
}
.cart-end-mobile-total {
    display: none;
}
@media (max-width: 992px) {
    .cart-list,
    .cart-actions {
        flex: 1 1 100%;
    }
    .cart-end-mobile-total {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        color: #c8c8c8;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .cart-end-mobile-total b {
        color: #ffffff;
        font-size: 20px;
    }
    .cart-end-block .cart-end-2 {
        margin-bottom: 10px;
    }
    .end-warning-text {
        margin-top: 6px;
    }
}
</style>
