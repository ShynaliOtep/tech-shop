<template>
    <div>
        <h5 class="white-text"> {{ $t('cart.title') }}</h5>
        <div v-if="!isCartEmpty">
            <div v-if="loading" class="main-loader center">
                <div class="col s12 center big loader-holder">
                    <div class="preloader-wrapper active">
                        <div class="spinner-layer spinner-orange-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                <input @click="setRentType('individual')" name="rent-type" type="radio" value="individual"/>
                                <span>Я укажу время аренды для каждого товара</span>
                            </label>
                        </p>
                    </form>
                    <button id="openModalBtn" class="btn orange darken-4 btn-with-icon">
                        <i class="material-icons prefix white-text">assignment</i>
                        Условия аренды
                    </button><br>
                </div>
            </div>
        </div>
        <div v-if="rentType === 'all'" class="col s12 m9 goods-list-rent-type-all">
                <div class="col s6 input-field beginning-date-field-rent-type-all">
                    <input
                        name="rent_start_date"
                        type="text"
                        class="datepicker white-text beginning-date-rent-type-all" required>
                </div>
                <div class="col s6 input-field white-text rent-starttime-field-rent-type-all">
                    <select
                        name="start_time"
                        class="white-text left rent-starttime-rent-type-all hide" required>
                        <option value="" disabled
                                selected>{{$t('translations.Choose time')}}:
                        </option>
                    </select>
                </div>
                <div class="col s6 input-field ending-datefield-rent-type-all hide">
                    <input
                        name="rent_end_date"
                        type="text" class="datepicker white-text endingdate-rent-type-all hide" required>
                </div>
                <div class="col s6 input-field white-text rent-endtime-field-rent-type-all hide">
                    <select name="end_time"
                            class="white-text left rent-end-time-rent-type-all hide" required>
                        <option value="" disabled selected>{{$t('translations.Rent end')}}
                            :
                        </option>
                    </select>
                </div>
                <ul class="grey darken-4 additionals-outerwrapper-rent-type-all hide">
                    <li>
                        <div class="grey darken-4">
                            <p>
                                {{$t('translations.Additional accessories')}}:
                            </p>

                        </div>
                        <div class="grey darken-4 additionalswrapper-rent-type-all">
                        </div>
                    </li>
                </ul>
        </div>

    </div>
    <div v-if="isCartEmpty">
        <h4 class="white-text center">{{ $t('translations.There is nothing here yet')}} :(</h4>
        <h5 class="white-text center">{{ $t('translations.Get back to')}} <a href="/"
                                                                            class="orange-text"><b><u>{{ $t('translations.to main page')}}</u></b></a>,
            {{ $t('translations.and')}}
            {{ $t('translations.add anything you like to your cart')}}</h5>
    </div>
</template>

<script>
import axios from 'axios';
import cartStore from '../store/cart.js';

export default {
    data() {
        return {
            cart: [],
            loading: false,
            isCartEmpty: true,
            rentType: null
        };
    },
    mounted() {
        this.getCart();
    },
    methods: {
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
            })
            this.cart = items;
            if (items.length > 0) {
                this.isCartEmpty = false;
            }
            this.loading = false;
        },
        async updateQuantity(id, action) {
            await axios.post('/api/cart/update', { id, action });
            this.getCart();
        },
        setRentType(type) {
            this.rentType = type;
        }
    },
};
</script>
