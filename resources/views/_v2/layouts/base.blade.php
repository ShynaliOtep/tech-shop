<?php $locale = session('locale', config('app.locale')); ?>
    <!doctype html>
<html lang="{{$locale}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=no">
    <title>PixelRental</title>
    @stack('styles')
{{--    <link rel="stylesheet" href="{{asset('css/materialize.css')}}">--}}
{{--    <link href="{{asset('css/material-icons.css')}}" rel="stylesheet">--}}
    <link href="{{  asset('css/v2/app.css?v=14') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{asset('favicon-32x32.png')}}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{asset('apple-touch-icon.png')}}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Exo 2", sans-serif;
            font-optical-sizing: auto;
        }

        html, body {
            height: 100%;
        }
        .app {
            display: flex;
        }
        .sidebar {
            width: 383px;
            background-color: #191919;
        }

        .base-section {
            width: calc(100vw - 383px);
            background-color: #151515;
        }
        .content {
            min-height: 100vh;
            padding: 40px 60px;
        }
        @media (max-width: 1440px) {
            .sidebar {
                width: 280px;
            }
            .base-section {
                width: calc(100vw - 280px);
            }
        }
        @media (max-width: 992px) {
            .sidebar {
                display: none;
            }
            .base-section {
                width: 100%;
            }
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="sidebar">
            @include('_v2.components.navigation.sidebar')
        </div>
        <section class="base-section">
            @include('_v2.components.navigation.navbar')
            @include('_v2.components.navigation.breadcrumbs')
            <div class="content">
                @yield('content')
            </div>
            <div>
                @include('_v2.components.navigation.footer')
            </div>
        </section>
        @include('_v2.components.navigation.bottom-navbar')
        @if(!session()->get('is_select_city'))
            <div
                style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1);"
                id="modal_location" class="modal" onclick="hideModalLocation()">
                <div class="black-block simple-centred-block modal-block ">
                    <span class="close" onclick="hideModalLocation()">
                       <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                        <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
                        </svg>
                    </span>
                    <p class="modal-title big-white-title mb-20">
                        Выберите город
                    </p>
                    <p class="grey-s-light-text mb-20">
                        Это нужно, чтобы показать товары и предложения, доступные именно в вашем регионе.
                    </p>
                    <div class="location-btns">
                        <a   class="black-btn mini-btn mb-20"  href="{{route('selectCity', 1)}}">{{__('translations.Almaty')}}</a>
                        <a  class="black-btn mini-btn mb-20" href="{{route('selectCity', 2)}}">{{__('translations.Astana')}}</a>
                    </div>
    {{--          --}}
    {{--                <a href="/auth/register" class="black-btn">Создать аккаунт</a>--}}
                </div>
            </div>
        @endif
    </div>
    <style>
        .location-btns {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        @media (max-width: 600px) {
            /*.location-btns {*/
            /*    flex-wrap: wrap;*/
            /*}*/
        }
    </style>
    <script>
        function hideModalLocation() {
            document.getElementById('modal_location').style.display = 'none'
        }

    </script>
<script src="{{asset('js/script.js?v=2')}}"></script>
<script src="{{asset('js/dropdown.js?v=2')}}"></script>
<script src="{{asset('js/materialize.js?v=2')}}"></script>
@stack('scripts')
</body>
</html>
