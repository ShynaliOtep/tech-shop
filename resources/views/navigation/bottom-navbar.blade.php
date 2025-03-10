<?php
$url =request()->path();
?>

<div class="bottom-navbar hide-on-med-and-up">
    <div class="bottom-navbar-list">
        <a href="/" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == '/')  style="color: #e65100" @endif>
                home
            </i>
        </a>
        <a href="/categories" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'categories')  style="color: #e65100" @endif>
                menu
            </i>
        </a>
        <a href="/cart3" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'cart3')  style="color: #e65100" @endif>
                shopping_cart
            </i>
        </a>
        @auth('clients')
        <a href="/profile/favorite" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'profile/favorite')  style="color: #e65100" @endif>
                favorite_border
            </i>
        </a>
        @endauth
        @auth('clients')
            <a href="{{route('viewProfile')}}" class="bottom-navbar-item">
                <i class="large material-icons">
                    person
                </i>
            </a>
        @endauth
        @guest('clients')
        <a href="{{route('login')}}" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'auth/login')  style="color: #e65100" @endif>
                person
            </i>
        </a>
        @endguest
    </div>
</div>
