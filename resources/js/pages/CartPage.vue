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
                            </div><div class="gap-patch">
                            <div class="circle"></div>
                        </div><div class="circle-clipper right">
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




        <!-- Cart Items List -->
        <div class="row client-discount-holder">
            <div class="col s12 m3 additional-info white-text hide-on-med-and-up hide">
                <span class="grey-text"><u>{{$t('translations.Select rental periods for goods')}}</u></span>
                <p>{{$t('translations.All items that you have added to your cart are listed here.')}}</p>
                <p>{{$t('translations.Check each of them for compliance, and, if necessary, remove unnecessary ones.')}}</p>
                <p><a
                    class="orange-text text-darken-4"
                    href="/"><b><u>{{$t('translations.to main page')}}</u></b></a>.</p>
                <hr>
                <p><b>{{$t('translations.IMPORTANT!')}}</b></p>
                <p>{{$t('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract')}}</p>
                <p>{{$t('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays')}}</p>
                <hr>
            </div>
            <div class="col s12 m9 goods-list">
                <form  method="POST" id="order-placement-form">
                  <!--  @foreach($items as $item) -->

                    <div v-for="(item, index) in cart" class="row no-margin good-wrapper">
                        <a href="#" class="cancel-btn">
                            <i class="material-icons white-text ">clear</i>
                        </a>
                        <div class="col s5 m3 good-cart-image center">
                            <img src="{{item.good->attachment?.url}}" alt=""
                                 class="good-image">
                        </div>
                        <div class="col s7 m9 good-cart-additional-info white-text">
                            <p>{{$t('translations.Name')}}: <a href="/{{item.good.id}}"><b
                                class="orange-text text-darken-4">{{item.good.name_ru}}</b></a>
                            </p>
                            <p v-if="item.good.discount_cost && item.good.discount_cost !== 0">
                                {{$t('translations.Cost for day')}}: <s>{{item.good.cost}}</s> <b
                                class="orange-text text-darken-4">{{item.good.discount_cost}}</b>
                            </p>
                            <p v-else>
                                {{$t('translations.Cost for day')}}: <b class="orange-text text-darken-4">{{item.good.cost}}</b>
                            </p>
<!--                            <td>-->
<!--                                <button class="quantity-btn minus" data-product-id="{{ $item->id }}">-</button>-->
<!--                                <span class="quantity" id="quantity-{{ $item->id }}">{{ $item->quantity }}</span>-->
<!--                                <button class="quantity-btn plus" data-product-id="{{ $item->id }}">+</button>-->
<!--                            </td>-->
                            <p class="info-for-items-label">У каждого варианта свое свободное время!</p>
                            <div class="col s12 input-field white-text hide {{index}}">
<!--                                <select-->
<!--                                    name="{{$item->good->id . 'pixelrental' . $item->id}}[item-id]"-->
<!--                                    data-good-id="{{$item->good->id}}"-->
<!--                                    data-old-item-id="{{$item->id}}"-->
<!--                                    class="white-text left item-id-selector" required>-->
<!--                                    <option value="" disabled-->
<!--                                            selected>{{__('translations.Item id select')}}:-->
<!--                                    </option>-->
<!--                                </select>-->
                            </div>
                            <div class="col s6 input-field hide begining-date-field">
<!--                                <input-->
<!--                                    name="{{$item->good->id . 'pixelrental' . $item->id}}[rent_start_date]"-->
<!--                                    data-item-id="{{$item->id}}" type="text"-->
<!--                                    class="datepicker white-text begining-date" required>-->
                            </div>
                            <div class="col s6 input-field white-text hide rent-start-time-field">
<!--                                <select-->
<!--                                    name="{{$item->good->id . 'pixelrental' . $item->id}}[start_time]"-->
<!--                                    class="white-text left rent-start-time" required>-->
<!--                                    <option value="" disabled-->
<!--                                            selected>{{__('translations.Choose time')}}:-->
<!--                                    </option>-->
<!--                                </select>-->
                            </div>
                            <div class="col s6 input-field hide ending-date-field">
<!--                                <input-->
<!--                                    name="{{$item->good->id . 'pixelrental' . $item->id}}[rent_end_date]"-->
<!--                                    type="text" class="datepicker white-text ending-date" required>-->
                            </div>
                            <div class="col s6 input-field white-text hide rent-end-time-field">
<!--                                <select name="{{$item->good->id . 'pixelrental' . $item->id}}[end_time]"-->
<!--                                        class="white-text left rent-end-time" required>-->
<!--                                    <option value="" disabled selected>{{__('translations.Rent end')}}-->
<!--                                        :-->
<!--                                    </option>-->
<!--                                </select>-->
                            </div>
                            <ul class="grey darken-4 additionals-outer-wrapper hide">
                                <li>
                                    <div class="grey darken-4">
                                        <p>
                                            {{$t('translations.Additional accessories')}}:
                                        </p>

                                    </div>
                                    <div class="grey darken-4 additionals-wrapper">
                                    </div>
                                </li>
                            </ul>
<!--                            <div class="control-sum right"-->
<!--                                 data-good-cost="{{$item->good->discount_cost ?? $item->good->cost}}">-->
<!--                                <h5 class="inline">{{__('translations.Total')}}:-->
<!--                                    @if($item->good->discount_cost && $item->good->discount_cost != 0)-->
<!--                                    <span-->
<!--                                        class="good-cost-holder orange-text text-darken-4">{{$item->good->discount_cost / 100 * (100 - $clientDiscount)}}-->
<!--                                            </span>-->
<!--                                    {{__('translations.KZT')}}-->
<!--                                    @if(Auth::guard('clients')->id() && $client['discount'])-->
<!--                                    <br>-->
<!--                                    <span>({{__('translations.With mention of personal discount')}}): {{$client['discount']}}%</span>-->
<!--                                    @endif-->
<!--                                    @else-->
<!--                                    @auth('clients')-->
<!--                                    <span-->
<!--                                        class="good-cost-holder">{{$item->good->cost / 100 * (100 - $client['discount'])}}-->
<!--                                                </span>-->
<!--                                    {{__('translations.KZT')}}-->
<!--                                    @if(Auth::guard('clients')->id() && $client['discount'])-->
<!--                                    <br>-->
<!--                                    <span>({{__('translations.With mention of personal discount')}}): {{$client['discount']}}%</span>-->
<!--                                    @endif-->
<!--                                    @endauth-->
<!--                                    @guest('clients')-->
<!--                                    <span-->
<!--                                        class="good-cost-holder">{{$item->good->discount_cost ?? $item->good->cost}}-->
<!--                                                </span>-->
<!--                                    {{__('translations.KZT')}}-->
<!--                                    @endguest-->
<!--                                    @endif-->
<!--                                </h5>-->
<!--                            </div>-->
                        </div>
                    </div>
<!--                    @endforeach-->
                </form>
            </div>÷
            <div class="col s12 m3 additional-info white-text hide-on-med-and-down hide">
                <span class="grey-text"><u>{{$t('translations.Select rental periods for goods')}}</u></span>
                <p>{{$t('translations.All items that you have added to your cart are listed here.')}}</p>
                <p>{{$t('translations.Check each of them for compliance, and, if necessary, remove unnecessary ones.')}}</p>
                <p><a
                    class="orange-text text-darken-4"
                    href="/"><b><u>{{$t('translations.to main page')}}</u></b></a>.</p>
                <hr>
                <p><b>{{$t('translations.IMPORTANT!')}}</b></p>
                <p>{{$t('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract')}}</p>
                <p>{{$t('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays')}}</p>
            </div>
        </div>




    </div>

    <!-- Empty cart texts -->
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
