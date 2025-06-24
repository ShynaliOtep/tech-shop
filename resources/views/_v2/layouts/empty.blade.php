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
            background-color: #151515;
            padding: 50px;
        }
    </style>
</head>
<body>
<div class="app">
    <section class="empty-section">
        <div class="content">
            @yield('content')
        </div>
    </section>
</div>
<script src="{{asset('js/script.js')}}"></script>
{{--<script src="{{asset('js/dropdown.js')}}"></script>--}}
<script src="{{asset('js/materialize.js')}}"></script>
@stack('scripts')
</body>
</html>
