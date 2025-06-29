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
                Регистрация
            </h4>
            <form method="POST" action="{{route('register')}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div>
                    <input name="name" id="name" type="text" placeholder="{{__('translations.Full name')}}" value="+7"
                           class="p-input grey-s-text mb-20" required>
                </div>
                <div>
{{--                    <span class="orange-text">*{{__('translations.Full name surname patronymic')}}</span>--}}
                </div>
                <input type="hidden" name="ref" value="{{ request('ref') }}">
                <div>
                    <input name="phone" id="phone" type="tel" maxlength="12"
                           placeholder="+7__________"
                           class="p-input grey-s-text mb-20"
                           required>
                </div>
                <div>
                    <input name="iin" id="iin" type="tel" maxlength="12"
                           placeholder="{{__('translations.Iin')}}"
                           class="p-input grey-s-text mb-20"
                           required>
                </div>
                <div >
                    <input name="email" id="email" type="email" placeholder="{{__('translations.Email')}}"
                           class="p-input grey-s-text mb-20"
                           required>
                </div>
                <div>
                    <input name="instagram" id="instagram" type="text"
                           placeholder="{{__('translations.Your instagram')}}" class="p-input grey-s-text mb-20" required>
                </div>
                <div class="input-field file-field">
                    <div>
                        <input type="file" name="files[]" multiple="multiple" class="file-fieldi"  accept=".jpg,.jpeg,.png">
                    </div>
                    <svg  width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 20.4V15.5999C0 14.9372 0.537258 14.3999 1.2 14.3999C1.86274 14.3999 2.4 14.9372 2.4 15.5999V20.4C2.4 20.7182 2.52652 21.0234 2.75156 21.2484C2.97661 21.4735 3.28174 21.6 3.6 21.6H20.4C20.7183 21.6 21.0234 21.4735 21.2484 21.2484C21.4735 21.0234 21.6 20.7182 21.6 20.4V15.5999C21.6 14.9372 22.1373 14.3999 22.8 14.3999C23.4627 14.3999 24 14.9372 24 15.5999V20.4C24 21.3547 23.6204 22.2702 22.9453 22.9453C22.2702 23.6204 21.3548 24 20.4 24H3.6C2.64522 24 1.72982 23.6204 1.05469 22.9453C0.379557 22.2702 0 21.3547 0 20.4ZM10.8 15.5999V4.09664L6.84844 8.04825C6.37981 8.51689 5.62019 8.51689 5.15156 8.04825C4.68293 7.57962 4.68293 6.81999 5.15156 6.35136L11.1516 0.351285L11.243 0.269253C11.7143 -0.115172 12.4091 -0.088059 12.8484 0.351285L18.8484 6.35136L18.9305 6.44276C19.3149 6.9141 19.2878 7.60891 18.8484 8.04825C18.4091 8.48759 17.7143 8.51471 17.243 8.13028L17.1516 8.04825L13.2 4.09664V15.5999C13.2 16.2627 12.6627 16.7999 12 16.7999C11.3373 16.7999 10.8 16.2627 10.8 15.5999Z" fill="#404040"/>
                    </svg>
                    <div class="file-path-wrapper">
                        <input id="file-path" type="text" class="file-path p-input grey-s-text mb-20"
                               placeholder="{{__('translations.Id card help register')}}">
                    </div>
                </div>
                <div class="col s12">
{{--                    <span class="orange-text">*{{__('translations.ID card (from both sides)')}}</span>--}}
                </div>
                <div>
                    <input name="password" type="password" placeholder="{{__('translations.Password')}}"
                           class="p-input grey-s-text mb-20" required>
                </div>
                <div>
                    <input name="password_confirmation" type="password"
                           placeholder="{{__('translations.Password confirm')}}"
                           class="p-input grey-s-text mb-20" style="overflow: hidden" required>
                </div>
                <div>
                    <button type="submit" class="auth-main-btn mb-20">
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
            </form>
            <div class="auth-add-option">
                <span class="grey-s-text">Уже есть аккаунт? </span>
                <a href="/auth/login" class="orange-s-link">Войти</a>
            </div>
        </div>
    </div>
    <script>
        const input = document.getElementById('phone');

        input.addEventListener('input', function () {
            let value = input.value.replace(/[^\d]/g, ''); // убираем всё, кроме цифр

            if (!value.startsWith('7')) {
                value = '7' + value.slice(0, 10); // всегда +7
            } else {
                value = value.slice(0, 11); // максимум 11 цифр после +
            }

            input.value = '+' + value;
        });
@endsection
