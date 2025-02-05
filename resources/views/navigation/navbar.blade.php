<div class="navbar-fixed">
    <nav>
        <div class="nav-wrapper grey darken-3">
            <div class="nav-inner-wrapper">
                <a href="#" data-target="slide-out" class="sidenav-trigger hide-on-med-and-up"><i
                        class="material-icons">menu</i></a>
                <div class="brand-logo">
                    <div class="search-wrapper valign-wrapper hide-on-small-only input-field">
                        <input id="search" type="text"
                               class="validate browser-default text-white center-align autocomplete"
                               placeholder="{{__('translations.Search')}}">
                        <i class="material-icons">
                            search
                        </i>
                    </div>
                </div>
                <ul class="right nav-buttons">
                    <li class="nav-element center">
                        <a href="{{route('cart')}}" class="nav-link cart-link">
                            <i class="material-icons left navbar-icon">
                                shopping_cart
                            </i>
                            <span class="hide-on-med-and-down">
                                {{__('translations.Cart')}}
                            </span>
                            @if(isset($cartCount))
                                <span class="cart-counter-badge badge red white-text">
                                    <span class="in-cart-item-counter">
                                        {{$cartCount}}
                                    </span>
                            </span>
                            @endif
                        </a>
                    </li>
                    @auth('clients')
                        <li class="nav-element center">
                            <a href="{{route('getFavorites')}}" class="nav-link white-text">
                                <i class="material-icons left navbar-icon">
                                    favorite_border
                                </i>
                                <span class="hide-on-med-and-down">
                                {{__('translations.Favorites')}}
                            </span>
                            </a>
                        </li>
                        <li class="nav-element center">
                            <a href="#" class="nav-link dropdown-trigger white-text" data-target="profile-options">
                                <i class="material-icons left navbar-icon">
                                    account_circle
                                </i>
                                <span class="hide-on-med-and-down">
                                {{__('translations.Profile')}}
                            </span>
                            </a>

                            <ul id='profile-options' class='dropdown-content main-color white-text'>
                                <li><a href="{{route('getMyOrders')}}" class="profile-dropdown-link white-text">{{__('translations.My orders')}}</a></li>
                                <li><a href="{{route('viewProfile')}}" class="profile-dropdown-link white-text">{{__('translations.Check profile')}}</a></li>
                                <li class="divider" tabindex="-1"></li>
                                <li><a href="{{route('logout')}}" class="white-text profile-dropdown-link"><i
                                            class="material-icons orange-text">cancel</i>{{__('translations.Logout')}}</a></li>
                            </ul>
                        </li>
                    @endauth
                    @guest('clients')
                        <li class="nav-element center">
                            <a href="{{route('login')}}" class="nav-link orange darken-4 auth-link z-depth-3">
                                {{__('translations.Log in')}}
                            </a>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var elems = document.querySelectorAll('.autocomplete');
                    var data = {
                        @foreach($goodOptions as $goodOption)
                        "{{$goodOption['name']}}": "{{$goodOption['url']}}",
                        @endforeach
                    };
                    var instances = M.Autocomplete.init(elems, {
                        data: data,
                        limit: 5,
                        onAutocomplete: (item) => {
                            window.location.href = '/autofill/' + item
                        }
                    });
                });
            </script>
        @endpush
    </nav>
</div>
