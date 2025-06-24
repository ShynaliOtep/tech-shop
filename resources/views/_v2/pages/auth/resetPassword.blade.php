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
                {{__('translations.Restore password')}}
            </h4>
            <form method="POST" action="{{route('resetPasswordPost')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div>
                    <input name="email" id="email" type="email"
                           value="{{$email}}"
                           class="p-input grey-s-text mb-20"
                           readonly
                           required>
                </div>
                <div>
                    <input name="password" type="password" placeholder="{{__('translations.Password')}}"
                           class="p-input grey-s-text mb-20" required>
                </div>
                <div>
                    <input name="password_confirmation" type="password"
                           placeholder="{{__('translations.Password confirm')}}"
                           class="p-input grey-s-text mb-20" required>
                </div>
                <div>
                    <button type="submit" class="auth-main-btn mb-20">
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
        </form>
        </div>
    </div>
@endsection

