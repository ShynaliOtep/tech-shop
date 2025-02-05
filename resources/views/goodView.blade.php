@extends('app')
@section('content')
    <h4 class="white-text">{{$good['name_' . session()->get('locale', 'ru')]}}</h4>
    <div class="row">
        <div class="col s12 m6">
            @if($good->attachment()?->first()?->url())
                <img loading="lazy" class="materialboxed good-image z-depth-5" width="100%"
                     src="{{$good->attachment()?->first()?->url()}}">
            @else
                <img loading="lazy" src="{{asset('img/no-image.jpg')}}" class="materialboxed good-image z-depth-5" width="100%">
            @endif
        </div>
        <div class="col s12 m6 detailed-info white-text">
            <div class="detailed-info-wrapper">
                <h4 class="center no-margin">{{$good['name_' . session()->get('locale', 'ru')]}}</h4>
                @if(count(explode('-', $good['description_' . session()->get('locale', 'ru')])) > 0)
                <span class="flow-text">{{__('translations.Description')}}:</span>
                    @foreach(explode('-', $good['description_' . session()->get('locale', 'ru')]) as $desc)
                        <li>{{$desc}}</li>
                    @endforeach
                @else
                <span class="flow-text">{{__('translations.Description')}}:</span>
                    <p>{{$good->desc}}</p>
                @endif
                @if($good->discount_cost)
                    <span class="right">
                        <span class="chip small item-cost-holder">
                            <s>{{$good->cost}}</s>
                        </span>
                    <span class="chip red white-text large item-cost-holder">
                        {{$good->discount_cost}}
                    </span>
                {{__('translations.Tenge per day')}}
                </span>
                @else
                    <span class="right">
                        <span class="chip item-cost-holder">
                            {{$good->cost}}
                        </span>
                    {{__('translations.Tenge per day')}}
                    </span>
                @endif
            </div>
            <div class="info-btns-wrapper">
                @if($good->additionals != '[]' && count($good->getAdditionals()) > 0)
                    <h5>{{__('translations.In addition with this good people usually rent')}}: </h5>
                    @foreach($good->getAdditionals() as $additional)
                        <p>
                            <label>
                                <input type="checkbox" class="orange-text"
                                       data-additional-id="{{$additional->id}}"/>
                                <span>{{$additional['name_' . session()->get('locale', 'ru')]}} <span class="white-text">(+ {{$additional->additional_cost ?? $additional->cost}}тг)</span></span>
                            </label>
                        </p>
                    @endforeach
                @endif
            </div>
            <hr>
            <p class="grey-text text-darken-2">{{__('translations.Damage cost')}}:
                {{$good->damage_cost}}
                тг
            </p>
        </div>
        <div class="col s12">
            <hr>
            <div class="row view-btns-holder">
                <div class="col s6 center">
                    <a class="btn-large orange darken-4 auth-link confirm-order-btn no-margin add-to-cart-btn"
                       data-product-id="{{ $good->id }}">
                        <span class="hide-on-med-and-down good-view-action-btn">{{__('translations.To cart')}}</span>
                        <i class="material-icons">add_shopping_cart</i>
                    </a>
                </div>
                <div class="col s6 center">
                    @auth('clients')
                        @if (in_array($good->id, App\Models\Client::query()->find(Auth::guard('clients')->id())->favorites()->pluck('good_id')->toArray()))
                            <a class="btn-large orange remove-from-favorites-btn darken-4 auth-link confirm-order-btn no-margin"
                               data-product-id="{{$good->id}}">
                                <span
                                    class="hide-on-med-and-down good-view-action-btn">{{__('translations.Delete from favorites')}}</span>
                                <i class="material-icons">
                                    favorite
                                </i>
                            </a>
                        @else
                            <a class="btn-large orange add-to-favorites-btn darken-4 auth-link confirm-order-btn no-margin"
                               data-product-id="{{$good->id}}">
                                <span
                                    class="hide-on-med-and-down good-view-action-btn">{{__('translations.Add to favorites')}}</span>
                                <i class="large material-icons">
                                    favorite_border
                                </i>
                            </a>
                        @endif
                    @endauth
                    @guest('clients')
                        <a href="#auth-modal"
                           class="btn-large orange darken-4 auth-link confirm-order-btn no-margin modal-trigger">
                            <span
                                class="hide-on-med-and-down good-view-action-btn">{{__('translations.Add to favorites')}}</span>
                            <i class="material-icons">
                                favorite_border
                            </i>
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
    @if($good->related_goods != '[]' && count($good->getRelatedGoods()) > 0)
        <h4 class="white-text">{{__('translations.Similar goods')}}:</h4>
        <div class="carousel">
            @foreach($good->getRelatedGoods() as $relatedGood)
                <div class="carousel-item">
                    @include('goodCard', ['good' => $relatedGood])
                </div>
            @endforeach
        </div>
    @endif
    @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')])
    @push('scripts')
        <script src="{{asset('js/cart.js')}}"></script>
        <script src="{{asset('js/favoriteActions.js')}}"></script>
    @endpush
@endsection
