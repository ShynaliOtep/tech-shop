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

        .simple-section {
        }
        .content {
            min-height: 100vh;
            padding: 40px 60px;
        }
        @media (max-width: 600px) {
            .app {
                padding: 30px 20px;
            }
            .content {
                padding: 0;
            }
        }
    </style>
</head>
<body>
<div class="app">
    <div class="header-logo-simple">
        @include('_v2.components.logo')
        <div class="show-m" style="margin-top: 30px">
            <a href="{{ url()->previous() }}">
                <svg width="26" height="24" viewBox="0 0 26 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.73579e-07 13.7143L4.73844e-07 10.2857L25.7143 10.2857V13.7143L7.73579e-07 13.7143Z" fill="#404040"/>
                    <path d="M15.4286 24L0 14.148L3.85578e-07 9.73747L15.4286 0V4.4105L3.16871 11.9141L15.4286 19.5895L15.4286 24Z" fill="#404040"/>
                </svg>

            </a>
        </div>
    </div>
    <section class="simple-section">
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
