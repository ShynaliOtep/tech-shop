<a href="/{{$good->id}}">
    <div class="card good-card hoverable z-depth-5">
        <div class="card-image">
            @if($good->attachment()?->first()?->url())
                <img loading="lazy" src="{{$good->attachment()?->first()?->url()}}" class="card-presenter-image">
            @else
                <img loading="lazy" src="{{asset('img/no-image.jpg')}}" class="card-presenter-image">
            @endif
            @auth('clients')
                @if (in_array($good->id, App\Models\Client::query()->find(Auth::guard('clients')->id())->favorites()->pluck('good_id')->toArray()))
                    <a class="btn-floating remove-from-favorites-btn btn-large halfway-fab waves-effect waves-light darken-4" data-product-id="{{$good->id}}">
                        <i class="large material-icons">
                            favorite
                        </i>
                    </a>
                @else
                    <a class="btn-floating add-to-favorites-btn btn-large halfway-fab waves-effect waves-light darken-4" data-product-id="{{$good->id}}">
                        <i class="large material-icons">
                            favorite_border
                        </i>
                    </a>
                @endif
            @endauth
            @guest('clients')
                <a class="btn-floating add-to-favorites-btn btn-large halfway-fab waves-effect waves-light darken-4 modal-trigger"
                   href="#auth-modal">
                    <i class="large material-icons">
                        favorite_border
                    </i>
                </a>
            @endguest
        </div>
        <a href="{{route('viewGood', $good)}}">
        <div class="card-content">
            <span class="card-title black-text">
                {{$good['name_' . session()->get('locale', 'ru')]}}
            </span>
            @if($good->discount_cost)
                <span class="cost-label">
                    <span class="chip small">
                        <s>{{$good->cost}}</s>
                    </span>
                    <span class="chip red white-text large">
                        <b>{{$good->discount_cost}}</b>
                    </span>
                {{__('translations.Tenge per day')}}
                </span>
            @else
                <span class="cost-label black-text">
                    <span class="chip">
                        <b>{{$good->cost}}</b>
                    </span>
                {{__('translations.Tenge per day')}}
                </span>
            @endif
            @if($good->discount_cost)
                <a class="btn-floating discount-btn waves-effect waves-light red darken-4">
                    <i class="medium material-icons white-text">money_off</i>
                </a>
            @endif
        </div>
        </a>
        <a class="add-to-cart-btn waves-effect waves-light orange darken-4 good-add-to-card"
           data-product-id="{{ $good->id }}">
            <span class="default-text">Добавить в корзину</span>
            <span class="cart-controls" style="display: none;">
                <i class="add-to-cart-btn-delete tiny material-icons">delete</i>
                <span class="add-to-cart-btn-minus tiny material-icons">-</span>
                <span class="add-to-cart-btn-count">0</span>
                <span class="add-to-cart-btn-plus tiny material-icons">+</span>
            </span>
        </a>

    </div>
</a>
