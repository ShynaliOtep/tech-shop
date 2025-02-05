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
            <h5 class="white-text">{{__('translations.Create new account')}}</h5>
            <div class="row">
                <form class="col s12 auth-form-element" method="POST" action="{{route('register')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">account_circle</i>
                            <input name="name" id="name" type="text" placeholder="{{__('translations.Full name')}}"
                                   class="white-text" required>
                        </div>
                        <div class="col s12">
                            <span class="orange-text">*{{__('translations.Full name surname patronymic')}}</span>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">phone</i>
                            <input name="phone" id="phone" type="tel" maxlength="12"
                                   placeholder="{{__('translations.Phone')}}"
                                   class="white-text"
                                   required>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">credit_card</i>
                            <input name="iin" id="iin" type="tel" maxlength="12"
                                   placeholder="{{__('translations.Iin')}}"
                                   class="white-text"
                                   required>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">email</i>
                            <input name="email" id="email" type="email" placeholder="{{__('translations.Email')}}"
                                   class="white-text"
                                   required>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">
                                <img src="{{asset('/img/instagram.svg')}}" height="35px" alt="">
                            </i>
                            <input name="instagram" id="instagram" type="text"
                                   placeholder="{{__('translations.Your instagram')}}" class="white-text" required>
                        </div>
                        <div class="file-field input-field col s12 auth-form-element">
                            <div>
                                <input type="file" name="files[]" multiple="multiple" accept=".jpg,.jpeg,.png">
                            </div>
                            <div class="file-path-wrapper">
                                <i class="material-icons prefix white-text">attach_file</i>
                                <input id="file-path" class="file-path" type="text"
                                       placeholder="{{__('translations.Id card help register')}}">
                            </div>
                        </div>
                        <div class="col s12">
                            <span class="orange-text">*{{__('translations.ID card (from both sides)')}}</span>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">lock</i>
                            <input name="password" type="password" placeholder="{{__('translations.Password')}}"
                                   class="white-text password-field" required>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <i class="material-icons prefix white-text">done</i>
                            <input name="password_confirmation" type="password"
                                   placeholder="{{__('translations.Password confirm')}}"
                                   class="white-text password-field" required>
                        </div>
                        <div class="input-field col s12 auth-form-element">
                            <button type="submit" class="btn orange darken-4 authorization-link">
                                {{__('translations.Register')}}
                            </button>
                        </div>
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
            <span class="white-text">{{__('translations.Already have an account?')}}
                <a href="{{route('login')}}"
                   class="orange-text"><u>
                        {{__('translations.Log in')}}
</u>
                </a>
            </span>
        </div>
    </div>
</div>
<script src="{{asset('js/materialize.js')}}"></script>
</body>
</html>
