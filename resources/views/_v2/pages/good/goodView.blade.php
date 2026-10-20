@php
    /**
     * @var \App\Models\Good $good
     */
    @endphp
@extends('_v2.layouts.base')
@section('content')

    <div class="good-view-block">
        <div class="good-view-img">
            @if($good->attachment()?->first()?->url())
                <img loading="lazy" class="materialboxed good-image z-depth-5" width="100%"
                     src="{{$good->attachment()?->first()?->url()}}">
            @else
                <img loading="lazy" src="{{asset('img/image_3.png')}}" class="materialboxed good-image z-depth-5" width="100%">
            @endif
            <div class="">
                @auth('clients')
                    @if (in_array($good->id, App\Models\Client::query()->find(Auth::guard('clients')->id())->favorites()->pluck('good_id')->toArray()))
                        <a class="add-to-fav-view remove-to-fav-view"
                           data-product-id="{{$good->id}}">
                            <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.6364 7.80003C23.6364 6.36786 23.0757 4.99475 22.0783 3.98205C21.0809 2.96935 19.7287 2.40001 18.3182 2.40001C17.3966 2.40001 16.6792 2.532 16.0157 2.8254C15.3447 3.12219 14.6545 3.6169 13.8356 4.44845C13.3741 4.91709 12.6259 4.91709 12.1644 4.44845C11.3455 3.6169 10.6553 3.12219 9.98429 2.8254C9.32085 2.532 8.60342 2.40001 7.68182 2.40001C6.27135 2.40001 4.91905 2.96935 3.9217 3.98205C2.92434 4.99475 2.36364 6.36786 2.36364 7.80003C2.36364 9.73922 3.45033 11.3499 4.90501 12.8836L13 21.1032L20.4452 13.5434L21.0892 12.8825C22.5437 11.3353 23.6364 9.72767 23.6364 7.80003ZM26 7.80003C26 11.0876 23.8638 13.4986 22.1072 15.2473L22.1083 15.2485L13.8356 23.6485C13.3741 24.1172 12.6259 24.1172 12.1644 23.6485L3.90554 15.2626L3.22692 14.5758C1.62792 12.8989 0 10.6907 0 7.80003C0 5.73133 0.808758 3.74678 2.24938 2.28399C3.69 0.821204 5.64447 0 7.68182 0C8.84022 0 9.89589 0.16801 10.9284 0.624612C11.6531 0.945138 12.3285 1.39346 13 1.96993C13.6715 1.39346 14.3469 0.945138 15.0716 0.624612C16.1041 0.16801 17.1598 0 18.3182 0C20.3555 0 22.31 0.821204 23.7506 2.28399C25.1912 3.74678 26 5.73133 26 7.80003Z" fill="currentColor"/>
                            </svg>
                        </a>
                    @else
                        <a class="add-to-fav-view"
                           data-product-id="{{$good->id}}">
                            <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.6364 7.80003C23.6364 6.36786 23.0757 4.99475 22.0783 3.98205C21.0809 2.96935 19.7287 2.40001 18.3182 2.40001C17.3966 2.40001 16.6792 2.532 16.0157 2.8254C15.3447 3.12219 14.6545 3.6169 13.8356 4.44845C13.3741 4.91709 12.6259 4.91709 12.1644 4.44845C11.3455 3.6169 10.6553 3.12219 9.98429 2.8254C9.32085 2.532 8.60342 2.40001 7.68182 2.40001C6.27135 2.40001 4.91905 2.96935 3.9217 3.98205C2.92434 4.99475 2.36364 6.36786 2.36364 7.80003C2.36364 9.73922 3.45033 11.3499 4.90501 12.8836L13 21.1032L20.4452 13.5434L21.0892 12.8825C22.5437 11.3353 23.6364 9.72767 23.6364 7.80003ZM26 7.80003C26 11.0876 23.8638 13.4986 22.1072 15.2473L22.1083 15.2485L13.8356 23.6485C13.3741 24.1172 12.6259 24.1172 12.1644 23.6485L3.90554 15.2626L3.22692 14.5758C1.62792 12.8989 0 10.6907 0 7.80003C0 5.73133 0.808758 3.74678 2.24938 2.28399C3.69 0.821204 5.64447 0 7.68182 0C8.84022 0 9.89589 0.16801 10.9284 0.624612C11.6531 0.945138 12.3285 1.39346 13 1.96993C13.6715 1.39346 14.3469 0.945138 15.0716 0.624612C16.1041 0.16801 17.1598 0 18.3182 0C20.3555 0 22.31 0.821204 23.7506 2.28399C25.1912 3.74678 26 5.73133 26 7.80003Z" fill="currentColor"/>
                            </svg>
                        </a>
                    @endif
                @endauth
                @guest('clients')
                    <a href="#auth-modal" class="add-to-fav-view">
                        <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M23.6364 7.80003C23.6364 6.36786 23.0757 4.99475 22.0783 3.98205C21.0809 2.96935 19.7287 2.40001 18.3182 2.40001C17.3966 2.40001 16.6792 2.532 16.0157 2.8254C15.3447 3.12219 14.6545 3.6169 13.8356 4.44845C13.3741 4.91709 12.6259 4.91709 12.1644 4.44845C11.3455 3.6169 10.6553 3.12219 9.98429 2.8254C9.32085 2.532 8.60342 2.40001 7.68182 2.40001C6.27135 2.40001 4.91905 2.96935 3.9217 3.98205C2.92434 4.99475 2.36364 6.36786 2.36364 7.80003C2.36364 9.73922 3.45033 11.3499 4.90501 12.8836L13 21.1032L20.4452 13.5434L21.0892 12.8825C22.5437 11.3353 23.6364 9.72767 23.6364 7.80003ZM26 7.80003C26 11.0876 23.8638 13.4986 22.1072 15.2473L22.1083 15.2485L13.8356 23.6485C13.3741 24.1172 12.6259 24.1172 12.1644 23.6485L3.90554 15.2626L3.22692 14.5758C1.62792 12.8989 0 10.6907 0 7.80003C0 5.73133 0.808758 3.74678 2.24938 2.28399C3.69 0.821204 5.64447 0 7.68182 0C8.84022 0 9.89589 0.16801 10.9284 0.624612C11.6531 0.945138 12.3285 1.39346 13 1.96993C13.6715 1.39346 14.3469 0.945138 15.0716 0.624612C16.1041 0.16801 17.1598 0 18.3182 0C20.3555 0 22.31 0.821204 23.7506 2.28399C25.1912 3.74678 26 5.73133 26 7.80003Z" fill="currentColor"/>
                        </svg>
                    </a>
                @endguest
            </div>
                <a href="{{ url()->previous() }}" class="good-view-back-btn">
                    <svg width="32" height="30" viewBox="0 0 32 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.62676e-07 17.1429L5.89673e-07 12.8571L32 12.8571V17.1429L9.62676e-07 17.1429Z" fill="white"/>
                        <path d="M19.2 30L0 17.685L4.79831e-07 12.1718L19.2 0V5.51313L3.94328 14.8926L19.2 24.4869L19.2 30Z" fill="white"/>
                    </svg>
                </a>
        </div>
        <div class="good-view-content">
            <div class="good-v-c">
                <h4 class="good-v-title big-white-title mb-30">
                    {{$good['name_' . session()->get('locale', 'ru')]}}
                </h4>
                <div class="g-v-desc-out">
                    <p class="good-v-desc show-m">Описание</p>
                    @php
                        $descText = (string) $good['description_' . session()->get('locale', 'ru')];
                        // Пункты описания пишут как «- пункт». Делим только по дефису в начале строки
                        // или после пробела перед ним, чтобы «Wi-Fi» и «24-70mm» не разваливались.
                        $descItems = array_values(array_filter(array_map('trim', preg_split('/(?:^|\s)-\s*/u', $descText))));
                    @endphp
                    @if(count($descItems) > 1)
                        <ul>
                            @foreach($descItems as $desc)
                                <li class="white-s-text g-v-desc">{{$desc}}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="white-s-text g-v-desc">{{ $descText }}</p>
                    @endif
                </div>
                @php
                    $bookingClient = Auth::guard('clients')->check()
                        ? App\Models\Client::query()->find(Auth::guard('clients')->id())
                        : null;
                    $bookingPrice = $bookingClient
                        ? $good->getPriceForClient($bookingClient)
                        : ($good->getDiscountCost() ?? $good->cost);
                    $bookingProps = ['goodId' => $good->id, 'price' => (int) $bookingPrice, 'basePrice' => (int) $good->cost];
                @endphp
                <div class="good-booking">
                    <rent-booking data-props="{{ json_encode($bookingProps) }}">
                        {{-- Пока Vue не загрузился — хотя бы цена --}}
                        <p class="big-white-title g-v-price">{{ format_day_price($bookingPrice) }}</p>
                    </rent-booking>
                </div>
            </div>
        </div>
    </div>

    @if($good->related_goods != '[]' && count($good->getRelatedGoods()) > 0)
        <div class="related-block">
            <h4 class="big-white-title">{{__('translations.Similar goods')}}:</h4>
            <div class="related-goods">
                @foreach($good->getRelatedGoods() as $relatedGood)
                    <div class="related">
                        @include('_v2.components.good.goodCard', ['good' => $relatedGood])
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <style>
        .good-view-block {
            display: flex;
            gap: 60px;
        }
        .good-view-img, .good-view-content {
            width: 50%;
        }
        .good-view-content {
            display: flex;
            gap: 5px
        }
        .good-view-back-btn {
            position: absolute;
            top: 0;
            right: -40px;
            color: #ffffff;
        }
        .good-view-back-btn svg {
            width: 30px;
        }
        .good-v-c {
            width: 100%;
        }
        .related-block {
            width: 100%;
        }
        .related-goods {
            display: flex;
            gap: 20px;
            justify-content: left;
            flex-wrap: wrap;
        }
        .related {
            width: 350px;
            align-items: center;
        }
        .good-view-img {
            position: relative;
            align-self: flex-start;
        }
        @media (min-width: 993px) {
            /* Фото остаётся на экране, пока справа листается календарь */
            .good-view-img {
                position: sticky;
                top: 40px;
            }
        }
        .add-to-fav-view {
            position: absolute;
            bottom: 0;
            right: 0;
            color: #ffffff;
        }
        .remove-to-fav-view {
            color: #FF962E;
        }
        .g-v-desc-out {
            margin-bottom: 30px;
        }
        .good-v-desc {
            font-size: 20px;
            font-weight: 500;
            color: #ffffff;
        }
        @media (max-width: 992px) {
            .good-view-block {
                flex-wrap: wrap;
            }
            .good-view-img, .good-view-content {
                width: 100%;
            }
            .add-to-fav-view {
                top: 0;
                right: 0;
            }
            .good-view-back-btn {
                top: 0;
                left: 0;
                right: auto;
            }
            .good-view-back-btn svg {
                width: 25px;
            }
            .good-v-c ul{
                padding-left: 15px;
            }
            .g-v-desc {
                color: #c8c8c8;
                margin-top: 10px;
            }
            .good-v-c {
                display: flex;
                flex-wrap: wrap;
                flex-direction: column;
            }
            .good-v-title {
                order: 1;
            }
            .g-v-desc-out {
                order: 2;
            }
            .good-booking {
                order: 3;
            }
        }
    </style>


{{--    <h4 class="white-text">{{$good['name_' . session()->get('locale', 'ru')]}}</h4>--}}
{{--    <div class="row">--}}
{{--        <div class="col s12 m6">--}}
{{--            @if($good->attachment()?->first()?->url())--}}
{{--                <img loading="lazy" class="materialboxed good-image z-depth-5" width="100%"--}}
{{--                     src="{{$good->attachment()?->first()?->url()}}">--}}
{{--            @else--}}
{{--                <img loading="lazy" src="{{asset('img/no-image.jpg')}}" class="materialboxed good-image z-depth-5" width="100%">--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        <div class="col s12 m6 detailed-info white-text">--}}
{{--            <div class="detailed-info-wrapper">--}}
{{--                <h4 class="center no-margin">{{$good['name_' . session()->get('locale', 'ru')]}}</h4>--}}
{{--                @if(count(explode('-', $good['description_' . session()->get('locale', 'ru')])) > 0)--}}
{{--                    <span class="flow-text">{{__('translations.Description')}}:</span>--}}
{{--                    @foreach(explode('-', $good['description_' . session()->get('locale', 'ru')]) as $desc)--}}
{{--                        <li>{{$desc}}</li>--}}
{{--                    @endforeach--}}
{{--                @else--}}
{{--                    <span class="flow-text">{{__('translations.Description')}}:</span>--}}
{{--                    <p>{{$good->desc}}</p>--}}
{{--                @endif--}}
{{--                @if($good->discount_cost)--}}
{{--                    <span class="right">--}}
{{--                        <span class="chip small item-cost-holder">--}}
{{--                            <s>{{$good->cost}}</s>--}}
{{--                        </span>--}}
{{--                    <span class="chip red white-text large item-cost-holder">--}}
{{--                        {{$good->discount_cost}}--}}
{{--                    </span>--}}
{{--                {{__('translations.Tenge per day')}}--}}
{{--                </span>--}}
{{--                @else--}}
{{--                    <span class="right">--}}
{{--                        <span class="chip item-cost-holder">--}}
{{--                            {{$good->cost}}--}}
{{--                        </span>--}}
{{--                    {{__('translations.Tenge per day')}}--}}
{{--                    </span>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            <div class="info-btns-wrapper">--}}
{{--                @if($good->additionals != '[]' && count($good->getAdditionals()) > 0)--}}
{{--                    <h5>{{__('translations.In addition with this good people usually rent')}}: </h5>--}}
{{--                    @foreach($good->getAdditionals() as $additional)--}}
{{--                        <p>--}}
{{--                            <label>--}}
{{--                                <input type="checkbox" class="orange-text"--}}
{{--                                       data-additional-id="{{$additional->id}}"/>--}}
{{--                                <span>{{$additional['name_' . session()->get('locale', 'ru')]}} <span class="white-text">(+ {{$additional->additional_cost ?? $additional->cost}}тг)</span></span>--}}
{{--                            </label>--}}
{{--                        </p>--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            <hr>--}}
{{--            <p class="grey-text text-darken-2">{{__('translations.Damage cost')}}:--}}
{{--                {{$good->damage_cost}}--}}
{{--                тг--}}
{{--            </p>--}}
{{--        </div>--}}
{{--        <div class="col s12">--}}
{{--            <hr>--}}
{{--            <div class="row view-btns-holder">--}}
{{--                <div class="col s4 center">--}}
{{--                    --}}{{--                    <a class="btn-large orange darken-4 auth-link confirm-order-btn no-margin add-to-cart-btn"--}}
{{--                    --}}{{--                       data-product-id="{{ $good->id }}">--}}
{{--                    --}}{{--                        <span class="hide-on-med-and-down good-view-action-btn">{{__('translations.To cart')}}</span>--}}
{{--                    --}}{{--                        <i class="material-icons">add_shopping_cart</i>--}}
{{--                    --}}{{--                    </a>--}}

{{--                    <a class="add-to-cart-btn waves-effect waves-light orange darken-4 good-add-to-card"--}}
{{--                       data-product-id="{{ $good->id }}">--}}
{{--                        <span class="default-text add-to-cart-btn-text">Добавить в корзину</span>--}}
{{--                        <span class="cart-controls" style="display: none;">--}}
{{--                        <i class="add-to-cart-btn-delete tiny material-icons">delete</i>--}}
{{--                        <span class="add-to-cart-btn-minus tiny material-icons">-</span>--}}
{{--                        <span class="add-to-cart-btn-count">0</span>--}}
{{--                        <span class="add-to-cart-btn-plus tiny material-icons">+</span>--}}
{{--                    </span>--}}
{{--                    </a>--}}
{{--                </div>--}}
{{--                <div class="col s6 center">--}}
{{--                    @auth('clients')--}}
{{--                        @if (in_array($good->id, App\Models\Client::query()->find(Auth::guard('clients')->id())->favorites()->pluck('good_id')->toArray()))--}}
{{--                            <a class="btn-large orange remove-from-favorites-btn darken-4 auth-link confirm-order-btn no-margin"--}}
{{--                               data-product-id="{{$good->id}}">--}}
{{--                                <span--}}
{{--                                    class="hide-on-med-and-down good-view-action-btn">{{__('translations.Delete from favorites')}}</span>--}}
{{--                                <i class="material-icons">--}}
{{--                                    favorite--}}
{{--                                </i>--}}
{{--                            </a>--}}
{{--                        @else--}}
{{--                            <a class="btn-large orange add-to-favorites-btn darken-4 auth-link confirm-order-btn no-margin"--}}
{{--                               data-product-id="{{$good->id}}">--}}
{{--                                <span--}}
{{--                                    class="hide-on-med-and-down good-view-action-btn">{{__('translations.Add to favorites')}}</span>--}}
{{--                                <i class="large material-icons">--}}
{{--                                    favorite_border--}}
{{--                                </i>--}}
{{--                            </a>--}}
{{--                        @endif--}}
{{--                    @endauth--}}
{{--                    @guest('clients')--}}
{{--                        <a href="#auth-modal"--}}
{{--                           class="btn-large orange darken-4 auth-link confirm-order-btn no-margin modal-trigger">--}}
{{--                            <span--}}
{{--                                class="hide-on-med-and-down good-view-action-btn">{{__('translations.Add to favorites')}}</span>--}}
{{--                            <i class="material-icons">--}}
{{--                                favorite_border--}}
{{--                            </i>--}}
{{--                        </a>--}}
{{--                    @endguest--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')])
    @vite('resources/js/app.js')
    @push('scripts')
        <script src="{{asset('js/cart.js')}}"></script>
        <script src="{{asset('js/favoriteActions.js')}}"></script>
    @endpush

@endsection
