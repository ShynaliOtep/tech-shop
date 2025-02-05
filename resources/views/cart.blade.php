@extends('app')
@section('content')
    @if (count($items) > 0)
        <h5 class="white-text">{{__('translations.Cart')}}</h5>
        <div class="main-loader center">
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
                            <input name="rent-type" type="radio" value="all"/>
                            <span>Я укажу время аренды для всех товаров сразу</span>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input name="rent-type" type="radio" value="individual"/>
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
        <div class="col s12 m9 goods-list-rent-type-all hide">
            <form action="{{route('settleOrderAll')}}" method="POST" id="order-placement-form-rent-type-all">
                {{csrf_field()}}
                <div class="col s6 input-field beginning-date-field-rent-type-all hide">
                    <input
                        name="rent_start_date"
                        type="text"
                        class="datepicker white-text beginning-date-rent-type-all hide" required>
                </div>
                <div class="col s6 input-field white-text rent-starttime-field-rent-type-all hide">
                    <select
                        name="start_time"
                        class="white-text left rent-starttime-rent-type-all hide" required>
                        <option value="" disabled
                                selected>{{__('translations.Choose time')}}:
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
                        <option value="" disabled selected>{{__('translations.Rent end')}}
                            :
                        </option>
                    </select>
                </div>
                <ul class="grey darken-4 additionals-outerwrapper-rent-type-all hide">
                    <li>
                        <div class="grey darken-4">
                            <p>
                                {{__('translations.Additional accessories')}}:
                            </p>

                        </div>
                        <div class="grey darken-4 additionalswrapper-rent-type-all">
                        </div>
                    </li>
                </ul>
            </form>
        </div>
        <div class="row client-discount-holder hide" @if($client) data-discount-percent="{{$client['discount']}}" @endif>
            <div class="col s12 m3 additional-info white-text hide-on-med-and-up hide">
                <span class="grey-text"><u>{{__('translations.Select rental periods for goods')}}</u></span>
                <p>{{__('translations.All items that you have added to your cart are listed here.')}}</p>
                <p>{{__('translations.Check each of them for compliance, and, if necessary, remove unnecessary ones.')}}</p>
                <p><a
                        class="orange-text text-darken-4"
                        href="/"><b><u>{{__('translations.to main page')}}</u></b></a>.</p>
                <hr>
                <p><b>{{__('translations.IMPORTANT!')}}</b></p>
                <p>{{__('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract')}}</p>
                <p>{{__('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays')}}</p>
                <hr>
            </div>
            <div class="col s12 m9 goods-list hide">
                <form action="{{route('settleOrder')}}" method="POST" id="order-placement-form">
                    {{csrf_field()}}
                    @foreach($items as $item)
                        <div class="row no-margin good-wrapper" data-good-id="{{$item->good->id}}"
                             data-good-item-id="{{$item->id}}"
                             data-good-cost="{{$item->good->discount_cost ?? $item->good->cost}}">
                            <a href="#" class="cancel-btn"
                               data-product-id="{{$item->good->id . 'pixelrental' . $item->id}}">
                                <i class="material-icons white-text ">clear</i>
                            </a>
                            <div class="col s5 m3 good-cart-image center">
                                <img src="{{$item->good->attachment()?->first()?->url}}" alt=""
                                     class="good-image">
                            </div>
                            <div class="col s7 m9 good-cart-additional-info white-text">
                                <p>{{__('translations.Name')}}: <a href="/{{$item->good->id}}"><b
                                            class="orange-text text-darken-4">{{$item->good['name_' . session()->get('locale', 'ru')]}}</b></a>
                                </p>
                                @if($item->good->discount_cost && $item->good->discount_cost != 0)
                                    <p>{{__('translations.Cost for day')}}: <s>{{$item->good->cost}}</s> <b
                                            class="orange-text text-darken-4">{{$item->good->discount_cost}}</b>
                                    </p>
                                @else
                                    <p>{{__('translations.Cost for day')}}: <b class="orange-text text-darken-4">{{$item->good->cost}}</b></p>
                                @endif
                                <p class="info-for-items-label">У каждого варианта свое свободное время!</p>
                                <div class="col s12 input-field white-text hide {{$loop->index}}">
                                    <select
                                        name="{{$item->good->id . 'pixelrental' . $item->id}}[item-id]"
                                        data-good-id="{{$item->good->id}}"
                                        data-old-item-id="{{$item->id}}"
                                        class="white-text left item-id-selector" required>
                                        <option value="" disabled
                                                selected>{{__('translations.Item id select')}}:
                                        </option>
                                    </select>
                                </div>
                                <div class="col s6 input-field hide begining-date-field">
                                    <input
                                        name="{{$item->good->id . 'pixelrental' . $item->id}}[rent_start_date]"
                                        data-item-id="{{$item->id}}" type="text"
                                        class="datepicker white-text begining-date" required>
                                </div>
                                <div class="col s6 input-field white-text hide rent-start-time-field">
                                    <select
                                        name="{{$item->good->id . 'pixelrental' . $item->id}}[start_time]"
                                        class="white-text left rent-start-time" required>
                                        <option value="" disabled
                                                selected>{{__('translations.Choose time')}}:
                                        </option>
                                    </select>
                                </div>
                                <div class="col s6 input-field hide ending-date-field">
                                    <input
                                        name="{{$item->good->id . 'pixelrental' . $item->id}}[rent_end_date]"
                                        type="text" class="datepicker white-text ending-date" required>
                                </div>
                                <div class="col s6 input-field white-text hide rent-end-time-field">
                                    <select name="{{$item->good->id . 'pixelrental' . $item->id}}[end_time]"
                                            class="white-text left rent-end-time" required>
                                        <option value="" disabled selected>{{__('translations.Rent end')}}
                                            :
                                        </option>
                                    </select>
                                </div>
                                <ul class="grey darken-4 additionals-outer-wrapper hide">
                                    <li>
                                        <div class="grey darken-4">
                                            <p>
                                                {{__('translations.Additional accessories')}}:
                                            </p>

                                        </div>
                                        <div class="grey darken-4 additionals-wrapper">
                                        </div>
                                    </li>
                                </ul>
                                @php $clientDiscount = isset($client) ? $client['discount'] : 0 @endphp
                                <div class="control-sum right"
                                     data-good-cost="{{$item->good->discount_cost ?? $item->good->cost}}">
                                    <h5 class="inline">{{__('translations.Total')}}:
                                        @if($item->good->discount_cost && $item->good->discount_cost != 0)
                                            <span
                                                class="good-cost-holder orange-text text-darken-4">{{$item->good->discount_cost / 100 * (100 - $clientDiscount)}}
                                            </span>
                                            {{__('translations.KZT')}}
                                            @if(Auth::guard('clients')->id() && $client['discount'])
                                                <br>
                                                <span>({{__('translations.With mention of personal discount')}}): {{$client['discount']}}%</span>
                                            @endif
                                        @else
                                            @auth('clients')
                                                <span
                                                    class="good-cost-holder">{{$item->good->cost / 100 * (100 - $client['discount'])}}
                                                </span>
                                                {{__('translations.KZT')}}
                                                @if(Auth::guard('clients')->id() && $client['discount'])
                                                    <br>
                                                    <span>({{__('translations.With mention of personal discount')}}): {{$client['discount']}}%</span>
                                                @endif
                                            @endauth
                                            @guest('clients')
                                                <span
                                                    class="good-cost-holder">{{$item->good->discount_cost ?? $item->good->cost}}
                                                </span>
                                                {{__('translations.KZT')}}
                                            @endguest
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>
            <div class="col s12 m3 additional-info white-text hide-on-med-and-down hide">
                <span class="grey-text"><u>{{__('translations.Select rental periods for goods')}}</u></span>
                <p>{{__('translations.All items that you have added to your cart are listed here.')}}</p>
                <p>{{__('translations.Check each of them for compliance, and, if necessary, remove unnecessary ones.')}}</p>
                <p><a
                        class="orange-text text-darken-4"
                        href="/"><b><u>{{__('translations.to main page')}}</u></b></a>.</p>
                <hr>
                <p><b>{{__('translations.IMPORTANT!')}}</b></p>
                <p>{{__('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract')}}</p>
                <p>{{__('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays')}}</p>
            </div>
        </div>
        <div class="col s12 right-align hide" id="total-sum-of-items" >
            <h5 class="white-text">Итого: <span class="total-cost-holder">0</span> {{__('translations.KZT')}}</h5>
        </div>
        @auth('clients')
            <div class="col s12 right-align">
                <a href="#order-placement-modal"
                   class="btn orange darken-4 auth-link valign-wrapper next-step-btn modal-trigger">
                    {{__('translations.Place order')}}
                </a>
            </div>
        @endauth
        @guest('clients')
            <div class="col s12 right-align">
                <a href="#auth-modal"
                   class="btn orange darken-4 auth-link valign-wrapper next-step-btn modal-trigger">
                    {{__('translations.Place order')}}
                </a>
                @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.Please enter to your account to continue order settlement')])
            </div>
        @endguest
        <div id="myModal" class="modal-rent">
            <div class="modalcontent">
                <span class="close-btn" id="closeModalBtn">&times;</span>
                <p><b>{{__('translations.IMPORTANT!')}}</b></p>
                    <p>{{__('translations.Be sure to keep in mind that if you rent equipment and it breaks down, an additional payment will apply, Based on the terms of the contract')}}</p>
                    <p>{{__('translations.Please note: For late payment of payments specified in the agreement, the Lessor has the right require the Tenant to pay a penalty in the amount of 5% of the unpaid payment for each day delays')}}</p>
            </div>
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const modal = document.getElementById("myModal");
                        const openModalBtn = document.getElementById("openModalBtn");
                        const closeModalBtn = document.getElementById("closeModalBtn");

                        openModalBtn.onclick = function() {
                            modal.style.display = "block";
                        }

                        closeModalBtn.onclick = function() {
                            modal.style.display = "none";
                        }

                        window.onclick = function(event) {
                            if (event.target == modal) {
                                modal.style.display = "none";
                            }
                        }
                    });
                </script>
            @endpush
        </div>
        @include('confirmModal', [
            'modalClass' => 'order-placement-modal',
            'title' => __('translations.Are you sure you are ready to place an order?'),
            'subTitle' => __('translations.After confirming your order, your order will be transferred to the manager and you will be able to receive your goods at the pick-up point at') . ":",
            'link' => 'https://2gis.kz/almaty/firm/70000001069136996',
            'linkCaption' => __('translations.Tole BI street, 176'). '.',
            'downTitle' => __('translations.The manager will double-check your contact details and photographs of your ID, after which an equipment rental agreement will be signed').'.',
            'btnAction' => 'placeOrder',
            'btnCaption' => __('translations.Place order'),
        ])
    @else
        <h4 class="white-text center">{{__('translations.There is nothing here yet')}} :(</h4>
        <h5 class="white-text center">{{__('translations.Get back to')}} <a href="/"
                                                                            class="orange-text"><b><u>{{__('translations.to main page')}}</u></b></a>,
            {{__('translations.and')}}
            {{__('translations.add anything you like to your cart')}}</h5>
    @endif
    @push('scripts')
        <script src="{{asset('js/cartActions.js')}}"></script>
        <script src="{{asset('js/select.js')}}"></script>
    @endpush
@endsection
