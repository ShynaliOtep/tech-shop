@extends('_v2.layouts.simple')
@section('content')
    <div class="auth-login-page">
        <div class="black-block simple-centred-block">
            <a href="{{ url()->previous() }}" class="auth-back-btn grey-s-text">
                <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.51254e-07 8L2.76409e-07 6L15 6V8L4.51254e-07 8Z" fill="#404040"/>
                    <path d="M9 14L0 8.25298L2.24921e-07 5.68019L9 0V2.57279L1.84841 6.94988L9 11.4272L9 14Z" fill="#404040"/>
                </svg>
                <span>Назад</span>
            </a>
            <h4 class="big-white-title">
                {{__('translations.Your profile has been successfully created!')}}
            </h4>
            <p class="grey-s-text">
                {{__('translations.Check your inbox')}}
            </p>
            <a href="{{route('login')}}" class="black-btn mb-20">
                {{__('translations.Log in')}}
            </a>
        </div>
    </div>
@endsection
