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
            <h5 class="white-text">{{__('translations.Restore password')}}</h5>
            <div class="row">
                <form class="col s12 auth-form-element" method="POST" action="{{route('forgotPasswordPost')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">credit_card</i>
                            <input name="iin" id="iin" type="tel" maxlength="12"
                                   placeholder="{{__('translations.Iin')}}"
                                   class="white-text"
                                   required>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <button type="submit" class="btn orange darken-4 authorization-link">
                                {{__('translations.Restore password')}}
                            </button>
                        </div>
                        @if (session('message'))
                            <div class="col s12 auth-form-element">
                                <ul class="green-text">
                                    <li>{{session('message')}}</li>
                                </ul>
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="col s12 auth-form-element">
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
        </div>
    </div>
</div>
<script src="{{asset('js/materialize.js')}}"></script>
</body>
</html>
