<template>
    <div>
        <h5 class="white-text"> {{ $t('cart.title') }}</h5>
        <div v-if="!isCartEmpty">

            <!-- Loading Component -->
            <div v-if="loading" class="main-loader center">
                <div class="col s12 center big loader-holder">
                    <div class="preloader-wrapper active">
                        <div class="spinner-layer spinner-orange-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div>
                            <div class="gap-patch">
                                <div class="circle"></div>
                            </div>
                            <div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rent time types -->
            <div class="row">
                <div class="col s12 m6">
                    <h5 class="white-text">Укажите время аренды</h5>
                    <form action="#">
                        <p>
                            <label>
                                <input @click="setRentType('all')" name="rent-type" type="radio" value="all"/>
                                <span>Я укажу время аренды для всех товаров сразу</span>
                            </label>
                        </p>
                        <p>
                            <label>
                                <input @click="setRentType('individual')" name="rent-type" type="radio"
                                       value="individual"/>
                                <span>Я укажу время аренды для каждого товара</span>
                            </label>
                        </p>
                    </form>
                    <button id="openModalBtn" class="btn orange darken-4 btn-with-icon">
                        <i class="material-icons prefix white-text">assignment</i>
                        Условия аренды
                    </button>
                    <br>
                </div>
            </div>

            <!-- Указать начало и конец аренды на все товары -->
            <div v-if="rentType === 'all'" class="col s12 m9 goods-list-rent-type-all">
                <div class="col s6 input-field beginning-date-field-rent-type-all">
                    <input
                        v-model="date.dateStart"
                        :min="date.minStart"
                        name="rent_start_date"
                        @click="setMinEnd()"
                        type="date"
                        class="datepicker white-text beginning-date-rent-type-all" required>
                </div>
                <div class="col s6 input-field white-text rent-starttime-field-rent-type-all">

                    <select
                        style="margin-bottom: 8px"
                        v-if="date.dateStart !== null"
                        v-model="date.timeStart"
                        name="start_time"
                        class="datepicker white-text left rent-starttime-rent-type-all" required>
                        <option value="" disabled
                                selected>{{ $t('translations.Choose time') }}:
                        </option>
                        <option v-for="(item, key) in timeItems"
                                :value="item"
                        >
                            {{ item }}
                        </option>
                    </select>
                </div>
                <div class="col s6 input-field ending-datefield-rent-type-all">
                    <input
                        v-if="date.timeStart !== null"
                        v-model="date.dateEnd"
                        :min="date.minEnd"
                        name="rent_end_date"
                        type="date" class="datepicker white-text endingdate-rent-type-all" required>
                </div>
                <div class="col s6 input-field white-text rent-endtime-field-rent-type-all">
                    <select name="end_time"
                            v-if="date.dateEnd !== null"
                            v-model="date.timeEnd"
                            @change="retTypeAllDateReady"
                            class="datepicker white-text left rent-end-time-rent-type-all" required>
                        <option value="" disabled selected>{{ $t('translations.Rent end') }}
                            :
                        </option>
                        <option v-for="(item, key) in timeItems"
                                :value="item"
                        >
                            {{ item }}
                        </option>
                    </select>
                </div>
                <ul class="grey darken-4 additionals-outerwrapper-rent-type-all hide">
                    <li>
                        <div class="grey darken-4">
                            <p>
                                {{ $t('translations.Additional accessories') }}:
                            </p>

                        </div>
                        <div class="grey darken-4 additionalswrapper-rent-type-all">
                        </div>
                    </li>
                </ul>
            </div>



            <div class="row client-discount-holder">
                <div class="col s12 m3 additional-info white-text hide-on-med-and-up hide">
                    <span class="grey-text"><u>{{ $t('translations.Select rental periods for goods') }}</u></span>
                    <p>{{ $t('translations.All items that you have added to your cart are listed here.') }}</p>
                    <p>
                        {{ $t('translations.Check each of them for compliance, and, if necessary, remove unnecessary ones.') }}</p>
                    <p><a
                        class="orange-text text-darken-4"
                        href="/"><b><u>{{ $t('translations.to main page') }}</u></b></a>.</p>
                    <hr>
                    <p><b>{{ $t('translations.IMPORTANT!') }}</b></p>
                    <p>
                        {{ $t('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract') }}</p>
                    <p>
                        {{ $t('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays') }}</p>
                    <hr>
                </div>
                <div class="col s12 m9 goods-list">
                    <form method="POST" id="order-placement-form">
                        <!--  @foreach($items as $item) -->
                        <!-- FOR cartItems in cart -->
                        <div v-for="(good, index) in cart" class="row no-margin good-wrapper" :key="index">

                            <!-- IMAGE -->
                            <div class="col s5 m3 good-cart-image center">
                                <img src="{{good.attachment?.url}}" alt=""
                                     class="good-image">
                            </div>

                            <!-- Good info block -->
                            <div class="col s7 m9 good-cart-additional-info white-text">
                                <p>{{ $t('translations.Name') }}: <a href="/{{good.id}}"><b
                                    class="orange-text text-darken-4">{{ good.name_ru }}</b></a>
                                </p>
                                <p v-if="good.discount_cost && good.discount_cost !== 0">
                                    {{ $t('translations.Cost for day') }}: <s>{{ good.cost }}</s> <b
                                    class="orange-text text-darken-4">{{ good.discount_cost }}</b>
                                </p>
                                <p v-else>
                                    {{ $t('translations.Cost for day') }}: <b
                                    class="orange-text text-darken-4">{{ good.cost }}</b>
                                </p>

                                <!--  Кнопка - + и количества -->
                                <div class="add-to-cart-btn waves-effect waves-light orange darken-4 good-add-to-card">
                                    <span class="quantity-btn-minus" @click="minus(index)">
                                        <i v-if="good.quantity ===1" class="delete tiny material-icons">delete</i>
                                        <span v-else>-</span>
                                    </span>
                                    <span class="quantity">{{ good.quantity }}</span>
                                    <span class="quantity-btn-plus" @click="plus(index)">+</span>
                                </div>

                                <!-- Текст для доступности -->
                                <p v-if="good.availableItems == null" class="info-for-items-label">У каждого варианта
                                    свое свободное время!</p>
                                <p v-if="good.availableItems === false && good.availableItemsQuantity === 0" class="info-for-items-label">На выбранную дату
                                    нет свободных вариантов</p>
                                <p v-if="good.availableItems === false && good.availableItemsQuantity < good.quantity" class="info-for-items-label">
                                    На выбранную дату доступно: {{good.availableItemsQuantity}}</p>
                                <p v-if="good.availableItems === true" style="color: green"
                                   class="info-for-items-label">На выбранную дату есть свободных вариантов</p>
                                <div class="col s12 input-field white-text hide {{index}}">
                                </div>

                                <!-- Указать время для каждого товара -->
                                <div v-if="rentType === 'individual'">
                                    <div class="col s6 input-field begining-date-field">
                                        <input
                                            v-model="this.cart[index].date.dateStart"
                                            name="rent_start_date[{{index}}]"
                                            type="date"
                                            class="datepicker white-text beginning-date-rent-type-all" required>
                                    </div>
                                    <div class="col s6 input-field white-text rent-start-time-field">
                                        <select
                                            style="margin-bottom: 8px"
                                            v-if="good.date.dateStart !== null"
                                            v-model="good.date.timeStart"
                                            name="start_time[{{index}}]"
                                            class="datepicker white-text left rent-starttime-rent-type-all" required>
                                            <option value="" disabled
                                                    selected>{{ $t('translations.Choose time') }}:
                                            </option>
                                            <option v-for="(item, key) in timeItems"
                                                    :value="item"
                                            >
                                                {{ item }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col s6 input-field ending-date-field">
                                        <input
                                            v-if="good.date.timeStart !== null"
                                            v-model="good.date.dateEnd"
                                            name="rent_end_date[{{index}}]"
                                            type="date" class="datepicker white-text endingdate-rent-type-all" required>
                                    </div>
                                    <div class="col s6 input-field white-text rent-end-time-field">
                                        <select name="end_time [{{index}}]"
                                                v-if="good.date.dateEnd !== null"
                                                v-model="good.date.timeEnd"
                                                @change="retTypeIndividualDateReady(index)"
                                                class="datepicker white-text left rent-end-time-rent-type-all" required>
                                            <option value="" disabled selected>{{ $t('translations.Rent end') }}
                                                :
                                            </option>
                                            <option v-for="(item, key) in timeItems"
                                                    :value="item"
                                            >
                                                {{ item }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Блок ДОП. товары -->
                                <ul class="grey darken-4 additionals-outer-wrapper">
                                    <li>
                                        <div class="grey darken-4" v-if="good.additions">
                                            <p>
                                                {{ $t('translations.Additional accessories') }}:
                                            </p>

                                        </div>
                                        <div class="grey darken-4 additionals-wrapper" v-if="good.additions">
                                            <p v-for="(addition, additionalIndex) in good.additions">
                                                <label>
                                                    <input
                                                        @change="(event) => handleAdditional(event, index, additionalIndex)"
                                                        type="checkbox" class="orange-text additional-checkbox" v-model="addition.added" >
                                                    <span> {{ addition.good.name_ru }} <span
                                                        class="white-text">(+ {{ addition.good.additional_cost }}тг)</span></span>
                                                </label>

                                                <p v-if="addition.availableQuantity == null" class="info-for-items-label">У каждого варианта
                                                    свое свободное время!</p>
                                                <p v-if="addition.availableQuantity < addition.quantity" class="info-for-items-label">
                                                    На выбранную дату доступно: {{addition.availableQuantity}}</p>
                                                <p v-if="addition.availableQuantity >= addition.quantity" style="color: green"
                                                   class="info-for-items-label">На выбранную дату есть свободных вариантов</p>

                                                <div v-if="addition.added" class="add-to-cart-btn waves-effect waves-light orange darken-4 good-add-to-card">
                                                    <span class="quantity-btn-minus" @click="minusAdditional(index, additionalIndex)">
                                                        <i v-if="addition.quantity === 1" class="delete tiny material-icons">delete</i>
                                                        <span v-else>-</span>
                                                    </span>
                                                    <span class="quantity">{{addition.quantity }}</span>
                                                    <span class="quantity-btn-plus" @click="plusAdditional(index, additionalIndex)">+</span>
                                                </div>
                                            </p>
                                        </div>
                                    </li>
                                </ul>


                                <div class="control-sum right">
                                    <h5 v-if="good.sumPrice > 0" class="inline">{{ $t('translations.Total') }}:
                                        <span
                                            class="good-cost-holder">
                                        {{ good.sumPrice }}
                                                </span>
                                        {{ $t('translations.KZT') }}
                                    </h5>
                                </div>


                            </div>
                        </div>
                        <!--                    @endforeach-->
                    </form>
                </div>
                ÷
                <div class="col s12 m3 additional-info white-text hide-on-med-and-down hide">
                    <span class="grey-text"><u>{{ $t('translations.Select rental periods for goods') }}</u></span>
                    <p>{{ $t('translations.All items that you have added to your cart are listed here.') }}</p>
                    <p>
                        {{ $t('translations.Check each of them for compliance, and, if necessary, remove unnecessary ones.') }}</p>
                    <p><a
                        class="orange-text text-darken-4"
                        href="/"><b><u>{{ $t('translations.to main page') }}</u></b></a>.</p>
                    <hr>
                    <p><b>{{ $t('translations.IMPORTANT!') }}</b></p>
                    <p>
                        {{ $t('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract') }}</p>
                    <p>
                        {{ $t('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays') }}</p>
                </div>
                <div class="col s12 right-align" id="total-sum-of-items" v-if="endAvailable === true">
                    <h5 class="white-text">Итого: <span class="total-cost-holder">{{ sumPrice }}</span>
                        {{ $t('translations.KZT') }}</h5>
                    <p class="white-text" v-if="clientData && clientData.bonusPercent && clientData.bonusPercent > 0">
                        Бонус после покупки: <span class="bonus-holder">{{ bonusAmount }}</span>
                        {{ $t('translations.KZT') }}
                    </p>
                </div>
                <div class="col s12 right-align">
                    <div @click="modalOrder" href="#order-placement-modal" :class="!endAvailable? 'disabled' : ''"
                         class="btn orange darken-4 auth-link valign-wrapper next-step-btn modal-trigger">
                        {{ $t('translations.Place order') }}
                    </div>
                </div>
            </div>


        </div>

    </div>

    <!-- Empty cart texts -->
    <div v-if="isCartEmpty">
        <h4 class="white-text center">{{ $t('translations.There is nothing here yet') }} :(</h4>
        <h5 class="white-text center">{{ $t('translations.Get back to') }} <a href="/"
                                                                              class="orange-text"><b><u>{{ $t('translations.to main page') }}</u></b></a>,
            {{ $t('translations.and') }}
            {{ $t('translations.add anything you like to your cart') }}</h5>
    </div>
    <div v-if="modal.open && !isAuthenticated" class="modal bottom-sheet custom-modal open"
         style="z-index: 1003; display: block; opacity: 1; top: 10%; transform: scaleX(1) scaleY(1);">
        <div class="modal-content container center">
            <h1 class="btn-floating btn-large orange darken-4"><i
                class="large material-icons text-accent-4 white-text ">{{ 'favorite_border' }}</i></h1>
            <h4>{{ $t('translations.Authorization required') }}</h4>
            <p>{{ $t('translations.Please enter to your account to continue order settlement') }}</p>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <div class="row">
                <div class="col s12 center">
                    <a href="/auth/login" class="btn-large nav-link orange darken-4 auth-link ">
                        {{ $t('translations.Log in') }}
                    </a>
                </div>
                <div class="col s12 center">
                    <a href="/auth/register"
                       class="btn-large modal-btn grey black white-text register-link">{{ $t('translations.Register') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div v-if="modal.open" @click="modalClose()" class="modal-overlay"
         style="z-index: 1002; display: block; opacity: 0.5;"></div>
    <div v-if="modal.open && isAuthenticated" class="modal bottom-sheet custom-modal open"
         style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1);">
        <div class="modal-content container center">
            <h4>{{ $t('translations.Are you sure you are ready to place an order?') }}</h4>
            <p>{{
                    $t('translations.After confirming your order, your order will be transferred to the manager and you will be able to receive your goods at the pick-up point at') + ":"
                }}</p>
            <p>
                <a href="https://2gis.kz/almaty/firm/70000001069136996">{{
                        $t('translations.Tole BI street, 176') + '.'
                    }}</a>
            </p>
            <p>{{
                    $t('translations.The manager will double-check your contact details and photographs of your ID, after which an equipment rental agreement will be signed') + '.'
                }}</p>
        </div>
        <div class="divider"></div>
        <div class="modal-footer">
            <div class="row center">
                <p><b class="error-text red-text"></b></p>
                <button class="btn-large nav-link orange darken-4 auth-link confirm-order-btn" @click="orderEvent()">
                    {{ $t('translations.Place order') }}
                    <i class="material-icons">done</i>
                </button>
            </div>
        </div>
        <!--        <div class="modal-overlay" style="z-index: 1002; display: block; opacity: 0.5;"></div>-->
    </div>
</template>

<script>
import axios from 'axios';
//import  useTimeStore  from '../store/timeStore.js'
import cartStore from '../store/cart.js';

export default {
    data() {
        return {
            cart: [],
            loading: false,
            isCartEmpty: true,
            rentType: null,
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
                open: false
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
                url: 'item/get-available-items/' + cartItem.id,
                data: {
                    end_date: cartItem.date.dateEnd,
                    end_time: cartItem.date.timeEnd,
                    start_date: cartItem.date.dateStart,
                    start_time: cartItem.date.timeStart
                }
            })
            let items = response.data.available_items
            this.cart[index].availableItems = items.length >= cartItem.quantity
            this.cart[index].availableItemsQuantity = items.length
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
        retTypeIndividualDateReady(index) {
            this.getAvailableItems(index)
            this.getAvailableAdditions(index)
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
                this.calc(index)
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
                    cart: data
                }
            })
            if (response.data.success === true) {
                cartStore.clearCart()
                window.location.href = '/order/confirm-order?new_order=1'
                return
            } else {
                if (response.data.status === 'spamGuard') {
                    window.location.href = '/order/try-again-later'
                }
                if (response.data.status === 'authentication') {
                    alert(response.data.message)
                }
                alert('Что то пошло не так попробуйте еще раз')
            }
        }
    },
};
</script>

<style lang="scss" scoped>
.add-to-cart-btn {
    width: 50%;
}

select {
    display: block;
}

.quantity-btn-minus {
    padding: 0 20px;
    float: left;

    .delete {
        margin-top: 5px;
    }
}

.quantity-btn-plus {
    padding: 0 20px;
    float: right;
}
.add-to-cart-btn {
    padding-bottom: 10px;
}
</style>
