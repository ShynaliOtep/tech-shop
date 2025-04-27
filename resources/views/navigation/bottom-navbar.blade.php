<?php
$url =request()->path();
?>

<div class="bottom-navbar hide-on-med-and-up">
    <div class="bottom-navbar-list">
        <a href="/" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == '/')  style="color: #e37509" @endif>
                home
            </i>
        </a>
        <a href="/categories" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'categories')  style="color: #e37509" @endif>
                menu
            </i>
        </a>
        @auth('clients')
        <a href="/profile/favorite" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'profile/favorite')  style="color: #e37509" @endif>
                favorite_border
            </i>
        </a>
        @endauth
        <a href="/bonus" class="head-bonus-button">
            <div class="diamond"></div>
            О бонусах
        </a>
    </div>
</div>
