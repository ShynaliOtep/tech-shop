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
    <link rel="stylesheet" href="{{asset('css/materialize.css')}}">
    <link href="{{asset('css/material-icons.css')}}" rel="stylesheet">
    <link href="{{  asset('css/app.css') }}" rel="stylesheet">
    <link rel="icon" href="{{asset('favicon-32x32.png')}}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{asset('apple-touch-icon.png')}}">
</head>
<body>
@include('navigation.sidemenu')
<div class="main grey darken-4 ">
    <div class="row no-padding main-row">
        <div class="col s12 no-padding">
            @include('navigation.navbar')
        </div>
        <div class="col s12 no-padding">
            @include('navigation.breadcrumbs')
        </div>
        <div class="col s12 no-padding" id="main-content-hernya">
            <div class="container">
                @yield('content')
            </div>
        </div>
        <div class="col s12 no-padding">
            @include('navigation.footer')
        </div>
        @include('navigation.bottom-navbar')
    </div>
</div>
<script src="{{asset('js/script.js')}}"></script>
<script src="{{asset('js/dropdown.js')}}"></script>
<script src="{{asset('js/materialize.js')}}"></script>
@stack('scripts')
</body>
</html>
