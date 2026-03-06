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
                Вход в аккаунт
            </h4>
            <form method="POST" action="">
                {{csrf_field()}}
                <div>
                    <input
                        id="phone"
                        type="tel"
                        class="p-input grey-s-text mb-20"
                        placeholder="+7 (___) ___-__-__"
                        autocomplete="off"
                        inputmode="numeric"
                    />
                </div>
                <input type="hidden" name="phone" id="phone_hidden">
                <div>
                    <input name="password" type="password" placeholder="{{__('translations.Password')}}" class="p-input grey-s-text mb-20">
                </div>
                <div>
                    <button type="submit" class="auth-main-btn mb-20">
                        {{__('translations.Log in')}}
                    </button>
                </div>
                @if ($errors->any())
                    <div class="auth-form-element">
                        <ul class="red-text">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </form>
            <a href="{{route('register')}}" class="black-btn mb-20">{{__('translations.Register')}}</a>
            <div class="auth-add-option">
                <span class="grey-s-text">Забыли пароль? </span>
                <a href="{{route('forgotPassword')}}" class="orange-s-link">Восстановить</a>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/libphonenumber-js@1.11.7/bundle/libphonenumber-js.min.js"></script>
    <script>
        const input = document.getElementById('phone');
        const hidden = document.getElementById('phone_hidden');

        function formatPhone(digits) {

            if (!digits.startsWith('7')) {
                digits = '7' + digits;
            }

            if (digits.length > 1 && digits[1] !== '7') {
                digits = '77' + digits.slice(2);
            }

            digits = digits.substring(0, 11);

            let result = '+7';

            if (digits.length > 1) result += ' (' + digits.slice(1,4);
            if (digits.length >= 4) result += ') ' + digits.slice(4,7);
            if (digits.length >= 7) result += '-' + digits.slice(7,9);
            if (digits.length >= 9) result += '-' + digits.slice(9,11);

            return result;
        }

        input.addEventListener('input', () => {

            let digits = input.value.replace(/\D/g, '');

            if (digits.length === 0) {
                input.value = '';
                hidden.value = '';
                return;
            }

            input.value = formatPhone(digits);
            hidden.value = '+' + digits;
        });

        input.addEventListener('keydown', (e) => {

            if (e.key === 'Backspace') {

                let digits = input.value.replace(/\D/g, '');

                if (digits.length > 1) {
                    digits = digits.slice(0, -1);
                }

                input.value = formatPhone(digits);
                hidden.value = '+' + digits;

                e.preventDefault();
            }
        });
    </script>

@endsection
