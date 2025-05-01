<?php
$url =request()->path();
?>

<div class="bottom-navbar hide-on-med-and-up">
    <div class="bottom-navbar-list">
        <a href="/" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == '/')  style="color: #e37509" @endif>
                home
            </i>
           <span>Главная</span>
        </a>
        <a href="/categories" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'categories')  style="color: #e37509" @endif>
                menu
            </i>
            <span>Каталог</span>
        </a>
        @auth('clients')
        <a href="/profile/favorite" class="bottom-navbar-item">
            <i class="large material-icons" @if($url == 'profile/favorite')  style="color: #e37509" @endif>
                favorite_border
            </i>
            <span>Избранное</span>
        </a>
        @endauth
        <a href="/bonus" class="head-bonus-button">
            <img class="diamond-icon" src="/img/icon-diamond.png" alt="">
            Кэшбек
        </a>
    </div>
</div>
