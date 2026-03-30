<template>
    <div>
        <Loader v-if="loadingModal"/>
        <h5 v-if="!isCartEmpty"  class="big-white-title cart-page-title"> {{ $t('cart.title') }}</h5>
        <div v-if="!isCartEmpty" class="cart-page">
            <div class="cart-list">


                <div v-for="(good, index) in cart" class="cart-item-out" :key="index">
                    <div class="cart-item">
                        <div class="cart-item-img">
                            <img :src="good.attachment ? good.attachment[0].url : '/img/example.png'" alt="">
                        </div>
                        <div class="cart-info">
                            <div class="cart-text">
                                <p class="cart-title white-m-text mb-20">
                                    {{ good.name_ru }}
                                </p>
                                <div class="cart-cost">
                                <span class="cart-cost-1">
                                    {{(good.discount_cost && good.discount_cost !== 0 ) ? good.discount_cost * good.quantity :  good.cost * good.quantity}}₸
                                </span>
                                    <span class="cart-cost-2">
                                    {{good.quantity}}шт х {{(good.discount_cost && good.discount_cost !== 0 ) ? good.discount_cost :  good.cost}}₸/сутки
                                </span>
                                </div>
                            </div>
                            <div class="cart-btns">
                                <div class="cart-btn">
                                    <span class="quantity-btn-minus" @click="minus(index)">
                                        <img v-if="good.quantity ===1" class="car-delete" src="/img/delete-white.svg" alt="">
                                        <span v-else>-</span>
                                    </span>
                                    <span class="quantity">{{ good.quantity }}</span>
                                    <span class="quantity-btn-plus" @click="plus(index)">+</span>
                                </div>
                                <div class="cart-time-btn" v-if="rentType === 'individual'" :class="{filled: good.date.fill}" @click="openDateSet(index)">
                                    <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M16.3636 9.09091H1.81818V17.2727C1.81818 17.7748 2.2252 18.1818 2.72727 18.1818H15.4545C15.9566 18.1818 16.3636 17.7748 16.3636 17.2727V9.09091ZM11.8182 4.54545V3.63636H6.36364V4.54545C6.36364 5.04753 5.95662 5.45455 5.45455 5.45455C4.95247 5.45455 4.54545 5.04753 4.54545 4.54545V3.63636H2.72727C2.2252 3.63636 1.81818 4.04338 1.81818 4.54545V7.27273H16.3636V4.54545C16.3636 4.04338 15.9566 3.63636 15.4545 3.63636H13.6364V4.54545C13.6364 5.04753 13.2294 5.45455 12.7273 5.45455C12.2252 5.45455 11.8182 5.04753 11.8182 4.54545ZM18.1818 17.2727C18.1818 18.779 16.9608 20 15.4545 20H2.72727C1.22104 20 0 18.779 0 17.2727V4.54545C0 3.03922 1.22104 1.81818 2.72727 1.81818H4.54545V0.909091C4.54545 0.407014 4.95247 0 5.45455 0C5.95662 0 6.36364 0.407014 6.36364 0.909091V1.81818H11.8182V0.909091C11.8182 0.407014 12.2252 0 12.7273 0C13.2294 0 13.6364 0.407014 13.6364 0.909091V1.81818H15.4545C16.9608 1.81818 18.1818 3.03922 18.1818 4.54545V17.2727Z" fill="currentColor"/>
                                    </svg>
                                    <span v-if="good.date.fill">Сменить время</span>
                                    <span v-else>Указать время</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="date-ind"  v-if="rentType === 'individual' && good.date.open"
                         :class="{'red-dates': (good.availableItems === false && good.availableItemsQuantity === 0)}">
                        <div class="date-ind-el">
                            <p class="cart-dates-title white-s-text mb-10">
                                Начало аренды
                            </p>
                            <div class="cart-dates">
                                <DatePicker class="cart-date mb-20" v-model="good.date.dateStart"
                                            :min-date="today"
                                            @change="changeDateElement(index)"
                                />
                                <div class="cart-time">
                                    <Select
                                        v-model="good.date.timeStart"
                                        :options=timeItems
                                        @change="changeDateElement(index)"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="date-ind-el">
                            <p class="cart-dates-title white-s-text mb-10">
                                Конец аренды
                            </p>
                            <div class="cart-dates">
                                <DatePicker class="cart-date mb-20" v-model="good.date.dateEnd"
                                            :min-date="good.date.dateStart ? good.date.dateStart : today"
                                            @change="changeDateElement(index)"
                                />
                                <div class="cart-time">
                                    <Select
                                        v-model="good.date.timeEnd"
                                        :options=timeItems
                                        @change="changeDateElement(index)"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-if="good.availableItems === false && good.availableItemsQuantity === 0"  class="cart-status-text status-red" >
                        На выбранную дату нет свободных вариантов
                    </p>
                    <p v-if="good.availableItems === false && good.availableItemsQuantity < good.quantity" class="cart-status-text status-red">
                        На выбранную дату доступно: {{good.availableItemsQuantity}}
                    </p>
                    <p v-if="good.availableItems === true"
                       class="cart-status-text status-green">
                        На выбранную дату есть свободные варианты
                    </p>

                    <div class="cart-item-additions-out">
                        <p class="cart-additions-title" v-if="(good.additions && good.additions.length > 0)">
                            {{ $t('translations.Additional accessories') }}:
                        </p>
                        <div class="cart-item-additions" v-if="good.additions">
                            <div class="cart-item-addition" v-for="(addition, additionalIndex) in good.additions">
                                <label class="cio-in">
                                    <input
                                        name="checkbox-{{additionalIndex}}"
                                        @change="(event) => handleAdditional(event, index, additionalIndex)"
                                        type="checkbox" class="cia-checkbox" v-model="addition.added" >
                                    <div class="cio-before">
                                    </div>
                                    <div class="cart-item-addition-checkbox">
                                        <p class="cia-title mb-10">{{ addition.good.name_ru }}</p>
                                        <p class="cia-price">+{{ addition.good.additional_cost }}₸</p>
                                    </div>
                                </label>
                                <div v-if="addition.added" class="cart-btn">
                                    <span class="quantity-btn-minus" @click="minusAdditional(index, additionalIndex)">
                                        <img v-if="good.addition ===1" class="car-delete" src="/img/delete-white.svg" alt="">
                                        <span v-else>-</span>
                                    </span>
                                    <span class="quantity">{{ addition.quantity }}</span>
                                    <span class="quantity-btn-plus" @click="plusAdditional(index, additionalIndex)">+</span>
                                </div>

                                <!--                            <p v-if="addition.availableQuantity == null" class="info-for-items-label">У каждого варианта-->
                                <!--                                свое свободное время!</p>-->
                                <p v-if="addition.added && addition.availableQuantity < addition.quantity" class="cart-status-text status-red">
                                    На выбранную дату доступно: {{addition.availableQuantity}}
                                </p>
                                <p v-if="addition.added && addition.availableQuantity >= addition.quantity" class="cart-status-text status-green">
                                    На выбранную дату есть свободные варианты
                                </p>


                                <!--                            <div v-if="addition.added" class="add-to-cart-btn waves-effect waves-light orange darken-4 good-add-to-card">-->
                                <!--                                <span class="quantity-btn-minus" @click="minusAdditional(index, additionalIndex)">-->
                                <!--                                    <i v-if="addition.quantity === 1" class="delete tiny material-icons">delete</i>-->
                                <!--                                    <span v-else>-</span>-->
                                <!--                                </span>-->
                                <!--                                <span class="quantity">{{addition.quantity }}</span>-->
                                <!--                                <span class="quantity-btn-plus" @click="plusAdditional(index, additionalIndex)">+</span>-->
                                <!--                            </div>-->
                            </div>
                        </div>
                    </div>
                </div>



            </div>
            <div class="cart-actions">
                <p class="white-m-text mb-20">
                    Укажите время аренды
                </p>
                <div class="select-time mb-20">
                    <div :class="{'select-time-option': true, 'selected-option': rentType === 'all'}" @click="setRentType('all')">
                        <span class="option-circle">
                             <span class="option-circle-in"></span>
                        </span>
                        <span class="option-text">Для всех товаров</span>
                    </div>
                    <div :class="{'select-time-option': true, 'selected-option': rentType === 'individual'}" @click="setRentType('individual')">
                        <span class="option-circle">
                            <span class="option-circle-in"></span>
                        </span>
                        <span class="option-text">Индивидуально</span>
                    </div>
                </div>
                <div class="date-all"  v-if="rentType === 'all'">
                    <p class="cart-dates-title white-s-text mb-10">
                        Начало аренды
                    </p>
                    <div class="cart-dates">
                        <DatePicker
                            class="cart-date mb-20"
                            v-model="date.dateStart"
                            @change="changeDateElementAll()"
                            :min-date="today"
                        />
                        <div class="cart-time">
                            <Select
                                v-model="date.timeStart"
                                :options=timeItems
                                @change="changeDateElementAll()"
                            />
                        </div>
                    </div>
                    <p class="cart-dates-title white-s-text mb-10">
                        Конец аренды
                    </p>
                    <div class="cart-dates">
                        <DatePicker
                            class="cart-date mb-20"
                            v-model="date.dateEnd"
                            :min-date="date.dateStart ? date.dateStart : today"
                            @change="changeDateElementAll()"
                        />
                        <div class="cart-time">
                            <Select
                                v-model="date.timeEnd"
                                :options=timeItems
                                @change="changeDateElementAll()"
                            />
                        </div>
                    </div>

                </div>
                <div class="cart-end-block">
                    <p class="cart-sum-price">
                        {{ sumPrice }}₸
                    </p>
                    <div class="cart-end-2 mb-20">
                        <p class="cart-end-count">
                            {{cart.length}} товаров
                        </p>
                        <p class="cart-clear-btn" @click="clearCart">
                            <svg width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14.5455 5.45455H3.63636V17.2727C3.63636 17.4316 3.72489 17.6667 3.93821 17.88C4.15153 18.0933 4.38657 18.1818 4.54545 18.1818H13.6364C13.7952 18.1818 14.0303 18.0933 14.2436 17.88C14.4569 17.6667 14.5455 17.4316 14.5455 17.2727V5.45455ZM6.36364 14.5455V9.09091C6.36364 8.58883 6.77065 8.18182 7.27273 8.18182C7.77481 8.18182 8.18182 8.58883 8.18182 9.09091V14.5455C8.18182 15.0475 7.77481 15.4545 7.27273 15.4545C6.77065 15.4545 6.36364 15.0475 6.36364 14.5455ZM10 14.5455V9.09091C10 8.58883 10.407 8.18182 10.9091 8.18182C11.4112 8.18182 11.8182 8.58883 11.8182 9.09091V14.5455C11.8182 15.0475 11.4112 15.4545 10.9091 15.4545C10.407 15.4545 10 15.0475 10 14.5455ZM11.8182 2.72727C11.8182 2.56839 11.7297 2.33335 11.5163 2.12003C11.303 1.90671 11.068 1.81818 10.9091 1.81818H7.27273C7.11385 1.81818 6.87881 1.90671 6.66548 2.12003C6.45216 2.33335 6.36364 2.56839 6.36364 2.72727V3.63636H11.8182V2.72727ZM13.6364 3.63636H17.2727C17.7748 3.63636 18.1818 4.04338 18.1818 4.54545C18.1818 5.04753 17.7748 5.45455 17.2727 5.45455H16.3636V17.2727C16.3636 18.0229 15.9976 18.697 15.5291 19.1655C15.0606 19.634 14.3866 20 13.6364 20H4.54545C3.79524 20 3.1212 19.634 2.6527 19.1655C2.1842 18.697 1.81818 18.0229 1.81818 17.2727V5.45455H0.909091C0.407014 5.45455 0 5.04753 0 4.54545C0 4.04338 0.407014 3.63636 0.909091 3.63636H4.54545V2.72727C4.54545 1.97706 4.91148 1.30301 5.37997 0.834517C5.84847 0.366021 6.52252 0 7.27273 0H10.9091C11.6593 0 12.3334 0.366021 12.8018 0.834517C13.2703 1.30301 13.6364 1.97706 13.6364 2.72727V3.63636Z" fill="#FF962E"/>
                            </svg>
                            <span>Очистить корзину</span>
                        </p>
                    </div>
                    <div @click="modalOrder" href="#order-placement-modal" :class="!endAvailable? 'end-disabled' : ''"
                         class="orange-btn end-cart-btn">
                        Перейти к оформлению
                    </div>
                    <p v-if="!endAvailable" class="end-warning-text">
                        Вы должны указать время аренды
                    </p>
                </div>
            </div>
        </div>




        <!-- Empty cart texts -->
        <div v-if="isCartEmpty" class="cart-empty">
            <svg  class="cart-empty-icon mb-20" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M36.3636 20C36.3636 10.9626 29.0374 3.63636 20 3.63636C10.9626 3.63636 3.63636 10.9626 3.63636 20C3.63636 29.0374 10.9626 36.3636 20 36.3636C29.0374 36.3636 36.3636 29.0374 36.3636 20ZM14.1779 28.3683H14.1797V28.3665L14.1815 28.3647L14.1797 28.3629C13.5772 29.1657 12.4402 29.329 11.6371 28.7269C10.8338 28.1245 10.6708 26.9858 11.2731 26.1825L12.1431 26.8342C11.2938 26.1969 11.2732 26.1816 11.2731 26.1808L11.2766 26.1772C11.2781 26.1752 11.2801 26.1725 11.282 26.1701C11.2856 26.1653 11.2894 26.1589 11.2944 26.1523C11.3045 26.1392 11.3178 26.1224 11.3335 26.1026C11.3648 26.0631 11.4081 26.0115 11.4613 25.9482C11.5679 25.8211 11.7187 25.6476 11.9123 25.4457C12.2979 25.0434 12.8617 24.5142 13.5902 23.9844C15.041 22.9293 17.2264 21.8182 20 21.8182C22.7736 21.8182 24.959 22.9293 26.4098 23.9844C27.1383 24.5142 27.7021 25.0434 28.0877 25.4457C28.2813 25.6476 28.4321 25.8211 28.5387 25.9482C28.5919 26.0115 28.6352 26.0631 28.6665 26.1026C28.6822 26.1224 28.6955 26.1392 28.7056 26.1523C28.7106 26.1589 28.7144 26.1653 28.718 26.1701C28.7199 26.1725 28.7219 26.1752 28.7234 26.1772L28.7269 26.1808C28.7268 26.1816 28.7062 26.1969 27.8569 26.8342L28.7269 26.1825C29.3292 26.9858 29.1662 28.1245 28.3629 28.7269C27.5596 29.3292 26.421 29.1662 25.8185 28.3629V28.3647L25.8203 28.3665V28.3683L25.815 28.3612C25.8042 28.3475 25.7847 28.3206 25.7546 28.2848C25.6944 28.2131 25.5962 28.102 25.4634 27.9634C25.1956 27.684 24.793 27.3036 24.272 26.9247C23.2228 26.1617 21.7718 25.4545 20 25.4545C18.2282 25.4545 16.7772 26.1617 15.728 26.9247C15.207 27.3036 14.8044 27.684 14.5366 27.9634C14.4038 28.102 14.3056 28.2131 14.2454 28.2848C14.2153 28.3206 14.1958 28.3475 14.185 28.3612C14.1824 28.3645 14.1794 28.3664 14.1779 28.3683ZM14.5632 12.7273L14.7496 12.7362C15.6664 12.8293 16.3814 13.6041 16.3814 14.5455C16.3814 15.4868 15.6664 16.2616 14.7496 16.3548L14.5632 16.3636H14.5455C13.5413 16.3636 12.7273 15.5496 12.7273 14.5455C12.7273 13.5413 13.5413 12.7273 14.5455 12.7273H14.5632ZM25.4723 12.7273L25.6587 12.7362C26.5755 12.8293 27.2905 13.6041 27.2905 14.5455C27.2905 15.4868 26.5755 16.2616 25.6587 16.3548L25.4723 16.3636H25.4545C24.4504 16.3636 23.6364 15.5496 23.6364 14.5455C23.6364 13.5413 24.4504 12.7273 25.4545 12.7273H25.4723ZM40 20C40 31.0457 31.0457 40 20 40C8.9543 40 0 31.0457 0 20C0 8.9543 8.9543 0 20 0C31.0457 0 40 8.9543 40 20Z" fill="white"/>
            </svg>
            <h4 class="cart-empty-title big-white-title mb-20">Корзина пуста</h4>
            <p class="cart-empty-text mb-20">Воспользуйтесь каталогом или поиском, <br>
                чтобы найти всё, что нужно</p>
            <a href="/" class="orange-btn cart-empty-btn">Вернуться на главную</a>

        </div>

    </div>

    <div v-if="modal.info.open" @click="modalCloseInfo()" class="modal-overlay"
         style="z-index: 1002; display: block; opacity: 0.5;"></div>
    <div v-if="modal.info.open" id="modal" class="modal"
         style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1);"
    >
        <div class="black-block simple-centred-block modal-block ">
            <span class="close" @click="modalCloseInfo()">
                   <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                    <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
                    </svg>
                </span>
            <p class="modal-title big-white-title mb-20"><b>{{$t('translations.IMPORTANT!')}}</b></p>
            <p class="grey-s-light-text mb-20">{{$t('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract')}}</p>
            <p class="grey-s-light-text mb-20">{{$t('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays')}}</p>
        </div>
    </div>

    <div v-if="modal.open && !isAuthenticated"
         style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1);"
         id="modal" class="modal">
            <div class="black-block simple-centred-block modal-block ">
                <span class="close" @click="modalClose()">
                   <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                    <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
                    </svg>
                </span>
                <p class="modal-title big-white-title mb-20">
                    Войдите в аккаунт
                </p>
                <p class="grey-s-light-text mb-20">
                    Для того, чтобы создать заявку на аренду – Войдите или Создайте аккаунт
                </p>
                <a href="/auth/login" class="orange-btn mb-20">Войти</a>
                <a href="/auth/register" class="black-btn">Создать аккаунт</a>
            </div>
    </div>
    <div v-if="modal.open" @click="modalClose()" class="modal-overlay"
         style="z-index: 1002; display: block; opacity: 0.5;"></div>
    <div v-if="modal.open && isAuthenticated" id="modal" class="modal"
         style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1);">
        <div class="black-block simple-centred-block modal-block ">
                <span class="close" @click="modalClose()">
                   <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                    <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
                    </svg>
                </span>
            <p class="modal-title big-white-title mb-20">
                Подтвердите заказ
            </p>
            <div class="select-time mb-20">
                <div :class="{'select-time-option': true, 'selected-option': !isDelivery}" @click="() => {isDelivery = false}">
                        <span class="option-circle">
                             <span class="option-circle-in"></span>
                        </span>
                    <span class="option-text">Самовывоз</span>
                </div>
                <div v-if="clientData.order_count > 1" :class="{'select-time-option': true, 'selected-option': isDelivery}" @click="() => {isDelivery = true}">
                        <span class="option-circle">
                            <span class="option-circle-in"></span>
                        </span>
                    <span class="option-text">Доставка</span>
                </div>
            </div>

            <div v-if="isDelivery" class="mb-20 delivery-inputs">
                <p class="grey-s-light-text mb-20">После подтверждения оформления заказа ваш заказ будет передан менеджеру для обработки. Доставка будет выполнена по указанному адресу после подтверждения деталей с вами.</p>
                <BaseInput
                    v-model="delivery.street"
                    class="delivery-input"
                    placeholder="Улица/Микрорайон *"
                />
<!--                    :error="isDelivery && !delivery.street ? 'Обязательное поле' : ''"-->


                <BaseInput
                    v-model="delivery.house"
                    class="delivery-input"
                    placeholder="Дом *"
                />
<!--                    :error="isDelivery && !delivery.house ? 'Обязательное поле' : ''"-->

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
                      $t('translations.After confirming your order, your order will be transferred to the manager and you will be able to receive your goods at the pick-up point at') + ":"
                  }}
              </p>
              <a href="https://2gis.kz/almaty/firm/70000001069136996" class="cart-address">
                  <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M11.5492 6.29332C11.4873 5.03493 10.9641 3.84045 10.0751 2.94531C9.18622 2.05018 8.00004 1.52329 6.75038 1.46094L6.5 1.45455C5.15918 1.45455 3.87296 1.99058 2.92486 2.94531C1.97676 3.90004 1.44444 5.19526 1.44444 6.54545C1.44444 8.11587 2.32324 9.80882 3.46512 11.3196C4.56853 12.7794 5.83523 13.9609 6.5 14.5419C7.16477 13.9609 8.43147 12.7794 9.53488 11.3196C10.6768 9.80882 11.5556 8.11587 11.5556 6.54545L11.5492 6.29332ZM7.94444 6.54545C7.94444 5.74213 7.29774 5.09091 6.5 5.09091C5.70226 5.09091 5.05556 5.74213 5.05556 6.54545C5.05556 7.34878 5.70226 8 6.5 8C7.29774 8 7.94444 7.34878 7.94444 6.54545ZM9.38889 6.54545C9.38889 8.1521 8.09549 9.45455 6.5 9.45455C4.90451 9.45455 3.61111 8.1521 3.61111 6.54545C3.61111 4.93881 4.90451 3.63636 6.5 3.63636C8.09549 3.63636 9.38889 4.93881 9.38889 6.54545ZM13 6.54545C13 8.60631 11.8787 10.6203 10.6845 12.2003C9.62502 13.6021 8.43786 14.7534 7.69548 15.4212L7.4063 15.6768C7.394 15.6875 7.38121 15.6983 7.36822 15.7081C7.14927 15.8739 6.88853 15.9728 6.61708 15.995L6.5 16C6.22606 16 5.95864 15.9214 5.72841 15.7749L5.63178 15.7081L5.5937 15.6768C4.90377 15.077 3.52624 13.8022 2.31548 12.2003C1.12134 10.6203 0 8.60631 0 6.54545C0 4.80949 0.684605 3.14441 1.90359 1.9169C3.12258 0.689393 4.77609 0 6.5 0C8.22391 0 9.87742 0.689393 11.0964 1.9169C12.3154 3.14441 13 4.80949 13 6.54545Z" fill="#FF962E"/>
                  </svg>

                  {{
                      $t('translations.Tole BI street, 176') + '.'
                  }}</a>
          </div>

            <p class="grey-s-light-text mb-20">
                {{
                    $t('translations.The manager will double-check your contact details and photographs of your ID, after which an equipment rental agreement will be signed') + '.'
                }}
            </p>
            <button class="orange-btn  mb-20"
                    :class="{'order-disabled': !agree || (isDelivery  && (!delivery.street || !delivery.house)) }"
                    @click="orderEvent()">
                {{ $t('translations.Place order') }}
            </button>
            <div class="cart-agree">
                <label class="cio-in" style="margin: 0">
                    <input
                        name="checkbox-agree"
                        type="checkbox" class="cia-checkbox" v-model="agree" >
                    <div class="cio-before"></div>
                </label>
                <span class="grey-s-text">Я согласен с  </span>
                <p @click="modalOpenInfo()" class="orange-s-link">Условиями аренды</p>
            </div>
        </div>
        <!--        <div class="modal-overlay" style="z-index: 1002; display: block; opacity: 0.5;"></div>-->
    </div>
</template>

<script>
import axios from 'axios';

import cartStore from '../store/cart.js';
import '@vuepic/vue-datepicker/dist/main.css';
import DatePicker from '../components/DatePicker2.vue'
import Select from "../components/Select.vue";
import BaseInput from "../components/BaseInput.vue";
import Loader from "../components/Loader.vue";
export default {
     components: {Loader, Select, DatePicker, BaseInput },
    data() {
        return {
            cart: [],
            loading: false,
            isCartEmpty: true,
            rentType: 'all',
            date: {
                dateStart: null,
                timeStart: null,
                dateEnd: null,
                timeEnd: null,
                minStart: null,
                minEnd: null
            },
            timeItems: [],
            additions: [],
            isAuthenticated: false,
            clientData: null,
            endAvailable: false,
            sumPrice: 0,
            bonusAmount: 0,
            modal: {
                confirm: {
                    modalClass: String,
                    btnAction: Function
                },
                guest: {
                    modalClass: String,
                    btnAction: Function
                },
                info: {
                    open: false
                },
                open: false
            },
            agree: false,
            loadingModal: false,
            today: new Date().toISOString().split('T')[0],
            isDelivery: false,
            delivery: {
                street: null,
                house: null,
                apartment: null,
                entrance: null,
                floor: null,
                comment: null
            }
        };
    },
    mounted() {
       this.setDatesDefault()
        this.getCart();
        this.getDefaultTimeItems()
        this.getClientData()
    },
    methods: {
        setDatesDefault() {
            const today = new Date();
            // Форматируем дату в YYYY-MM-DD
            const formattedDate = today.toISOString().split('T')[0];
            this.date.minStart = formattedDate
            this.date.minEnd = formattedDate
        },

        /**
         * Получить корзину
         */
        async getCart() {
            this.loading = true;
            let cartItems = Object.keys(cartStore.getCart())
            const response = await axios.request({
                method: 'POST',
                url: '/api/cart/items',
                data: {
                    items: cartItems,
                }
            })
            console.log(response.data);
            let items = response.data.items

            items.forEach(item => {
                item.quantity = cartStore.getQuantity(item.id);
                item.availableItems = null
                item.additions = []
                item.addedAdditions = []
                item.sumCost = item.discount_cost ? item.discount_cost * item.quantity : item.cost * item.quantity
                item.sumPrice = 0
                item.date = {
                    open: false,
                    fill: false,
                    dateStart: null,
                    timeStart: null,
                    dateEnd: null,
                    timeEnd: null
                }
            })

            this.cart = items;

            if (items.length > 0) {
                this.isCartEmpty = false;
            }
            this.calcEnd()
            this.loading = false;
        },

        /**
         * Получить список times
         */
        async getDefaultTimeItems() {
            const response = await axios.request({
                method: 'POST',
                url: '/item/get-default-times',
            })
            console.log(response.data);
            this.timeItems = response.data.availableTimes
        },

        async getAvailableItems(index) {
            let cartItem = this.cart[index]
            const response = await axios.request({
                method: 'POST',
                url: 'api/get-available-count/' + cartItem.id,
                data: {
                    end_date: cartItem.date.dateEnd,
                    end_time: cartItem.date.timeEnd,
                    start_date: cartItem.date.dateStart,
                    start_time: cartItem.date.timeStart
                }
            })
            this.cart[index].availableItems = response.data.quantity >= cartItem.quantity
            this.cart[index].availableItemsQuantity = response.data.quantity
            this.calcEnd()
        },
        async getAvailableItemsAddition(index, additionalIndex) {
            let cartItem = this.cart[index]
            let addition = cartItem.additions[additionalIndex]
            const response = await axios.request({
                method: 'POST',
                url: 'api/get-available-count/' + addition.good.id,
                data: {
                    end_date: cartItem.date.dateEnd,
                    end_time: cartItem.date.timeEnd,
                    start_date: cartItem.date.dateStart,
                    start_time: cartItem.date.timeStart,
                }
            })
            addition.availableQuantity = response.data.quantity
            this.cart[index].additions[additionalIndex] = addition
        },

        async getAvailableAdditions(index) {
            let cartItem = this.cart[index]
            const response = await axios.request({
                method: 'POST',
                url: 'api/get-available-additions',
                data: {
                    endDate: cartItem.date.dateEnd,
                    endTime: cartItem.date.timeEnd,
                    startDate: cartItem.date.dateStart,
                    startTime: cartItem.date.timeStart,
                    goodId: cartItem.id
                }
            })
            let additions = response.data.additionals
            additions.forEach(addition =>  {
                addition.added = false
                addition.quantity = 0
                addition.availableQuantity = null
            })
            this.cart[index].additions = additions

            this.cart[index].additions.forEach((addition, additionIndex) => {
                this.getAvailableItemsAddition(index, additionIndex)
            })
        },
        async getClientData() {
            const responseAuth = axios.get('sanctum/csrf-cookie').then(response => {
                console.log(response)
            })

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            console.log(csrfToken)
            axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
            const response = await axios.request({
                method: 'GET',
                url: '/api/client',
                //   headers: { 'X-CSRF-TOKEN': csrfToken },
                withCredentials: true
            })
            console.log(response.data);
            this.clientData = response.data.clientData
            this.isAuthenticated = response.data.isAuthenticated
        },
        async order() {
            let data = []
            this.cart.forEach(cartItem => {
                let item = {
                    id: cartItem.id,
                    quantity: cartItem.quantity,
                    date: cartItem.date,
                    additions: cartItem.addedAdditions
                }
                data.push(item)
            })
            const response = await axios.request({
                method: 'POST',
                url: '/api/order',
                data: {
                    cart: data
                }
            })
            console.log(response.data);
            return response.data
        },
        retTypeAllDateReady() {
            this.setDatesAll()
            this.setAvailableItemsAll()
            this.setSumPriceAll()
        },
        async retTypeIndividualDateReady(index) {
           await this.getAvailableItems(index)
           await this.getAvailableAdditions(index)
        },
        setAvailableItemsAll() {
            for (let i = 0; i < this.cart.length; i++) {
                this.getAvailableItems(i)
                //if (this.cart[i].availableItems === true) {
                this.getAvailableAdditions(i)
                // }
            }
        },
        setDatesAll() {
            for (let i = 0; i < this.cart.length; i++) {
                this.cart[i].date = this.date
            }
        },
        setRentType(type) {
            this.rentType = type;
        },
        setSumPriceAll() {
            for (let i = 0; i < this.cart.length; i++) {
                this.setSumPrice(i)
            }
        },
        minus(index) {
            if (this.cart[index].quantity === 1) {
                cartStore.removeFromCart(this.cart[index].id)
                this.cart.splice(index, 1)
                this.calcEnd()
                return
            }
            this.cart[index].quantity--;
            cartStore.decreaseQuantity(this.cart[index].id)
            if (this.cart[index].availableItems !== null) {
                this.getAvailableItems(index)
            }
            this.calc(index)
        },
        plus(index) {
            this.cart[index].quantity++;
            cartStore.addToCart(this.cart[index].id)
            if (this.cart[index].availableItems !== null) {
                this.getAvailableItems(index)
            }
            this.calc(index)
        },
        minusAdditional(index, additionalIndex) {
            if (this.cart[index].additions[additionalIndex].quantity === 1) {
                this.removeAddition(index, additionalIndex)
                this.calc(index)
                return
            }

            this.cart[index].additions[additionalIndex].quantity--;
            this.calc(index)
        },
        plusAdditional(index, additionalIndex) {
            this.cart[index].additions[additionalIndex].quantity++;
            // if (this.cart[index].availableItems !== null) {
            //     this.getAvailableItems(index)
            // }
            this.calc(index)
        },
        addedAdditional(index, additionalGoodId) {
            for (let i = 0; i < this.cart[index]. addedAdditions.length; i++) {
                if (this.cart[index].addedAdditions[i].goodId === additionalGoodId) {
                    return this.cart[index].addedAdditions[i];
                }
            }
            return false
        },
        indexAddedAdditional(index, additionalGoodId) {
            for (let i = 0; i < this.cart[index]. addedAdditions.length; i++) {
                if (this.cart[index].addedAdditions[i].goodId === additionalGoodId) {
                    return i;
                }
            }
            return false
        },
        isAddedAdditional(index, additionalGoodId) {
            this.cart[index].addedAdditions.forEach(addedAddition => {
                if (addedAddition.goodId === additionalGoodId) {
                    return true
                }
            })
            return false
        },
        calc(index) {
            let cartItem = this.cart[index]
            console.log(cartItem.quantity)
            let sumCost = cartItem.discount_cost ? cartItem.discount_cost * cartItem.quantity : cartItem.cost * cartItem.quantity
            cartItem.additions.forEach(addition => {
                if (addition.added === true) {
                    sumCost = sumCost + (addition.good.additional_cost * addition.quantity)
                }
            })
            this.cart[index].sumCost = sumCost
            this.calcEnd()
        },
        handleAdditional(event, index, additionalIndex) {
            console.log('f')
            if (event.target.checked) {
                this.addAddition(index, additionalIndex);
            } else {
                this.removeAddition(index, additionalIndex);
            }
        },
        addAddition(index, additionIndex) {
            let item = this.cart[index]
            let addition = item.additions[additionIndex]

            addition.added = true
            addition.quantity = 1
            item.additions[additionIndex] = addition

            this.cart[index] = item
            this.calc(index)
        },
        removeAddition(index, additionIndex) {
            let item = this.cart[index]
            let addition = item.additions[additionIndex]

            addition.added = false
            addition.quantity = 0
            item.additions[additionIndex] = addition

            this.cart[index] = item
            this.calc(index)
        },
        setEndAvailable() {
            let available = true;
            for (let i = 0; i < this.cart.length; i++) {
                let cartItem = this.cart[i];
                if (cartItem.availableItems !== true) {
                    this.endAvailable = false
                    return
                }
                for (let j = 0; j < cartItem.additions.length; j++) {
                    let addition = cartItem.additions[j]
                    if (addition.availableQuantity < addition.quantity) {
                        this.endAvailable = false
                        return;
                    }
                }
            }
            console.log('calcEndAvailable:', available)
            this.endAvailable = available
        },
        calcEnd() {
            if (this.cart.length === 0) {
                this.isCartEmpty = true
            }
            let sumPrice = 0;
            for (let i = 0; i < this.cart.length; i++) {
                sumPrice = sumPrice + this.cart[i].sumCost
            }
            this.sumPrice = sumPrice
            if (this.clientData && this.clientData.bonusPercent) {
                this.bonusAmount = this.clientData.bonusPercent * sumPrice / 100
            }
            this.setEndAvailable()
        },
        modalOrder() {
            if (!this.endAvailable) {
                return
            }

            this.modal.open = true;
            document.body.style.overflow = 'hidden'
        },
        modalClose() {
            this.modal.open = false;
            document.body.style.overflow = ''
        },
        setMinEnd() {
            // console.log(this.date.dateStart)
            // const today = new Date(this.date.dateStart);
            // this.date.minEnd = today.toISOString().split('T')[0]
        },
        setSumPrice(index){
            let item = this.cart[index]
            item.sumPrice = item.sumCost * this.diffInDays(item.date.dateStart, item.date.dateEnd)
            this.cart[index] = item
        },
        diffInDays(dateFrom, dateTo) {
            const from = new Date(dateFrom)
            const to = new Date(dateTo)

            const diffInMs = to - from
            const diffInDays = diffInMs / (1000 * 60 * 60 * 24)
            return Math.max(diffInDays, 1)
        },
        async orderEvent() {
            this.loadingModal = true
            if (!this.agree) {
                return
            }
            let data = []
            this.cart.forEach(cartItem => {
                let additions = []
                cartItem.additions.forEach(addition => {
                    additions.push({
                       goodId: addition.good.id,
                       quantity: addition.quantity
                    });
                })
                let item = {
                    id: cartItem.id,
                    quantity: cartItem.quantity,
                    date: cartItem.date,
                    additions: additions
                }
                data.push(item)
            })
            console.log(data);
            const response = await axios.request({
                method: 'POST',
                url: '/api/order',
                data: {
                    cart: data,
                    is_delivery: this.isDelivery,
                    delivery: this.delivery
                }
            })
            this.loadingModal = false
            if (response.data.success === true) {
                cartStore.clearCart()
                window.location.href = '/profile/orders?new_order=1'
                return
            } else {
                if (response.data.status === 'spamGuard') {
                    window.location.href = '/order/try-again-later'
                }
                if (response.data.status === 'authentication') {
                    alert(response.data.message)
                }
                if (response.data.status === 'notAvailable') {
                    alert(response.data.message)
                }
                alert('Что то пошло не так попробуйте еще раз')
            }
        },
        async changeDateElement (index) {
            const date = this.cart[index].date
            if (date.dateStart !== null && date.timeStart !== null && date.dateEnd !== null && date.timeEnd !== null) {
                await this.retTypeIndividualDateReady(index)
                if (this.cart[index].availableItems === true) {
                    date.open = false
                    date.fill = true
                }
            }
            this.cart[index].date = date
        },
        openDateSet(index) {
            this.cart[index].date.open = !this.cart[index].date.open
        },
        changeDateElementAll() {
            const date = this.date;
            if (date.dateStart !== null && date.timeStart !== null && date.dateEnd !== null && date.timeEnd !== null) {
                this.retTypeAllDateReady()
            }
        },
        clearCart() {
            cartStore.clearCart()
            this.cart = []
            this.sumPrice = 0
            this.isCartEmpty = true
        },
        closeModal() {
            this.modal.open = false
        },
        modalCloseInfo() {
            this.modal.info.open = false;
            document.body.style.overflow = ''
            this.modalOrder()
        },
        modalOpenInfo() {
            this.modal.info.open = true;
            document.body.style.overflow = 'hidden'
            this.closeModal()
        }
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
    footer {
        padding-bottom: 300px !important;
    }
}
</style>
