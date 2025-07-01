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
    <link href="{{  asset('css/v2/app.css') }}" rel="stylesheet">
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
        @media (max-width: 600px) {
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
    </div>
<script src="{{asset('js/script.js?v=2')}}"></script>
<script src="{{asset('js/dropdown.js?v=2')}}"></script>
<script src="{{asset('js/materialize.js?v=2')}}"></script>
@stack('scripts')
</body>
</html>
