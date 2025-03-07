<?php

?>

<div class="bottom-navbar hide-on-med-and-up">
    <div class="bottom-navbar-list">
        <a href="/" class="bottom-navbar-item">
            <i class="large material-icons">
                home
            </i>
        </a>
        <a href="/categories" class="bottom-navbar-item">
            <i class="large material-icons">
                menu
            </i>
        </a>
        <a href="/cart3" class="bottom-navbar-item">
            <i class="large material-icons">
                shopping_cart
            </i>
        </a>
        @auth('clients')
        <a href="/profile/favorite" class="bottom-navbar-item">
            <i class="large material-icons">
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
            <i class="large material-icons">
                person
            </i>
        </a>
        @endguest
    </div>
</div>
