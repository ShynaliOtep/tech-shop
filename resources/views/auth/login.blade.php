<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>pixelrental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{  asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
@include('auth.header')
<div class="container valign-wrapper auth-container">
    <div class="valign-wrapper">
        <div class="main-color center auth-inner">
            <a href="/" class="back-auth-button"><i class="material-icons orange-text text-lighten-3">cancel</i></a>
            <h5 class="white-text">{{__('translations.Log into your account')}}</h5>
            <div class="row">
                <form class="col s12" method="POST" action="">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="input-field col s12">
                            <i class="material-icons prefix white-text">phone</i>
                            <input name="phone" type="tel" placeholder="{{__('translations.Phone')}}" class="white-text">
                        </div>
                        <div class="input-field col s12">
                            <i class="material-icons prefix white-text">lock</i>
                            <input name="password" type="password" placeholder="{{__('translations.Password')}}" class="white-text">
                        </div>
                        <div class="input-field col s12">
                            <button type="submit" class="btn orange darken-4 authorization-link">
                                {{__('translations.Log in')}}
                            </button>
                        </div>
                        @if ($errors->any())
                            <div class="col s12">
                                <ul class="red-text">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    <hr>
                </form>
            </div>
            <span class="white-text">{{__('translations.Still do not have an account?')}} <a href="{{route('register')}}" class="orange-text"><u>{{__('translations.Register')}}</u></a></span>
            <br>
            <a href="{{route('forgotPassword')}}" class="orange-text" style="margin-right: auto"><u>{{__('translations.Forgot password')}}</u></a>
        </div>
    </div>
</div>
@include('navigation.bottom-navbar')
</body>
</html>
