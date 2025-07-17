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
                <a hhref="{{ url()->previous() }}" class="good-view-back-btn">
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
                    @if(count(explode('-', $good['description_' . session()->get('locale', 'ru')])) > 0)
                        <ul>
                            @foreach(explode('-', $good['description_' . session()->get('locale', 'ru')]) as $desc)
                                <li class="white-s-text g-v-desc">{{$desc}}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="white-s-text g-v-desc">{{$good->desc}}</p>
                    @endif
                </div>
                <p class="big-white-title g-v-price">
                    @if($good->discount_cost)
                        <s>{{$good->cost}}</s>
                        {{$good->discount_cost}}
                        {{__('translations.Tenge per day')}}
                    @else
                        {{$good->cost}}
                        {{__('translations.Tenge per day')}}
                    @endif
                </p>
                <a class="add-to-cart-btn good-add-to-card"
                   data-product-id="{{ $good->id }}" data-max-count="{{$good->items()->count()}}">
                    <span class="add-to-cart-btn-text">
                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.49129 18.1792C4.49149 17.1778 5.30081 16.3592 6.30769 16.3592L6.49412 16.369C7.41244 16.4628 8.12389 17.2405 8.12409 18.1792C8.12409 19.1806 7.31454 19.9998 6.30769 20C5.30068 20 4.49129 19.1807 4.49129 18.1792ZM14.4708 18.1792C14.471 17.1778 15.2804 16.3593 16.2872 16.3592L16.4737 16.369C17.3922 16.4626 18.1034 17.2404 18.1036 18.1792C18.1036 19.1807 17.2942 20 16.2872 20C15.2803 19.9999 14.4708 19.1807 14.4708 18.1792ZM2.72371 0L2.80361 0.00355114C3.19788 0.0381376 3.52837 0.326075 3.61238 0.719105L4.43092 4.557H19.0909C19.3664 4.557 19.6268 4.68215 19.7993 4.89702C19.9718 5.11188 20.0381 5.39322 19.9786 5.66229L18.4818 12.4334L18.4809 12.4325C18.348 13.0386 18.0135 13.5817 17.531 13.9719C17.0484 14.3623 16.4473 14.5763 15.8265 14.5774H6.95133V14.5765C6.32402 14.5858 5.71276 14.3781 5.22104 13.9879C4.72427 13.5936 4.38052 13.0385 4.24803 12.4183V12.4174L1.98774 1.81818H0V0H2.72371ZM6.02626 12.0384L6.04579 12.1156C6.09997 12.2927 6.20658 12.4491 6.35119 12.5639C6.51642 12.695 6.72157 12.7638 6.93091 12.7592H15.8229C16.0279 12.7589 16.2279 12.6885 16.3884 12.5586C16.549 12.4287 16.6616 12.2467 16.7063 12.0428V12.041L17.9589 6.37518H4.81888L6.02626 12.0384Z" fill="white"/>
                        </svg>
                        <span class="good-add-to-card-text">Добавить</span>
                    </span>
                    <span class="cart-controls" style="display: none;">
                        <svg class="add-to-cart-btn-delete" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14.5455 5.45455H3.63636V17.2727C3.63636 17.4316 3.72489 17.6667 3.93821 17.88C4.15153 18.0933 4.38657 18.1818 4.54545 18.1818H13.6364C13.7952 18.1818 14.0303 18.0933 14.2436 17.88C14.4569 17.6667 14.5455 17.4316 14.5455 17.2727V5.45455ZM6.36364 14.5455V9.09091C6.36364 8.58883 6.77065 8.18182 7.27273 8.18182C7.77481 8.18182 8.18182 8.58883 8.18182 9.09091V14.5455C8.18182 15.0475 7.77481 15.4545 7.27273 15.4545C6.77065 15.4545 6.36364 15.0475 6.36364 14.5455ZM10 14.5455V9.09091C10 8.58883 10.407 8.18182 10.9091 8.18182C11.4112 8.18182 11.8182 8.58883 11.8182 9.09091V14.5455C11.8182 15.0475 11.4112 15.4545 10.9091 15.4545C10.407 15.4545 10 15.0475 10 14.5455ZM11.8182 2.72727C11.8182 2.56839 11.7297 2.33335 11.5163 2.12003C11.303 1.90671 11.068 1.81818 10.9091 1.81818H7.27273C7.11385 1.81818 6.87881 1.90671 6.66548 2.12003C6.45216 2.33335 6.36364 2.56839 6.36364 2.72727V3.63636H11.8182V2.72727ZM13.6364 3.63636H17.2727C17.7748 3.63636 18.1818 4.04338 18.1818 4.54545C18.1818 5.04753 17.7748 5.45455 17.2727 5.45455H16.3636V17.2727C16.3636 18.0229 15.9976 18.697 15.5291 19.1655C15.0606 19.634 14.3866 20 13.6364 20H4.54545C3.79524 20 3.1212 19.634 2.6527 19.1655C2.1842 18.697 1.81818 18.0229 1.81818 17.2727V5.45455H0.909091C0.407014 5.45455 0 5.04753 0 4.54545C0 4.04338 0.407014 3.63636 0.909091 3.63636H4.54545V2.72727C4.54545 1.97706 4.91148 1.30301 5.37997 0.834517C5.84847 0.366021 6.52252 0 7.27273 0H10.9091C11.6593 0 12.3334 0.366021 12.8018 0.834517C13.2703 1.30301 13.6364 1.97706 13.6364 2.72727V3.63636Z" fill="white"/>
                        </svg>
                        <span class="add-to-cart-btn-minus add-to-cart-btns">-</span>
                        <span class="add-to-cart-btn-count">0</span>
                        <span class="add-to-cart-btn-plus add-to-cart-btns">+</span>
                    </span>
                </a>

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
                color: #404040;
                margin-top: 20px;
            }
            .good-v-c {
                display: flex;
                flex-wrap: wrap;
                flex-direction: column;
            }
            .good-v-title {
                order: 1;
            }
            .add-to-cart-btn {
                order: 2;
            }
            .g-v-price {
                order: 3;
            }
            .g-v-desc-out {
                order: 4;
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
    @push('scripts')
        <script src="{{asset('js/cart.js')}}"></script>
        <script src="{{asset('js/favoriteActions.js')}}"></script>
    @endpush

@endsection
