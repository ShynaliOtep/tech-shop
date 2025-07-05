@extends('_v2.layouts.base')
@section('content')
    @if(isset($carousel))
        <div>
            <carousel></carousel>
        </div>
        @vite('resources/js/app.js')
    @endif


{{--    @include('_v2.components.banner')--}}
    @if(isset($news))
        <div>
            <h5 class="page-type-title">{{__('translations.news block')}}</h5>
            <div class="items horizontal-scrolling-news" id="carousel">
                @foreach($news as $good)
                    <div class="item">
                        @include('_v2.components.good.goodCard', ['good' => $good, 'isNew' => true])
                    </div>
                @endforeach
            </div>
            <div class="dots" id="carousel-dots"></div>
        </div>
    @endif
    @isset($viewedGoodTypes)
        @foreach($viewedGoodTypes as $goodType)
            <div class="row">
                <h5 class="page-type-title">{{__('translations.' . $goodType->code)}}</h5>
                @if(count($goodType->goods) != 0)
                    <div class="items">
                        @foreach($goodType->goods as $good)
                            <div class="item">
                                @include('_v2.components.good.goodCard', ['good' => $good])
                            </div>
                        @endforeach
                    </div>
                @else
                    <h5 style="text-align: center" class="white-m-text center">{{__('translations.There is nothing here yet')}} :(</h5>
                @endif
            </div>
        @endforeach
    @endisset
    @push('scripts')
        <script src="{{asset('js/favoriteActions.js?v=1')}}"></script>
        <script src="{{asset('js/cart.js?v=2')}}"></script>
    @endpush
    <div
         style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1); display: none"
         id="modal-fav" class="modal" onclick="hideModalFav()">
        <div class="black-block simple-centred-block modal-block ">
                <span class="close" onclick="hideModalFav()">
                   <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                    <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
                    </svg>
                </span>
            <p class="modal-title big-white-title mb-20">
                Войдите в аккаунт
            </p>
            <p class="grey-s-light-text mb-20">
                Для добавления товара в "любимые" необходимо аутентифицироваться
            </p>
            <a href="/auth/login" class="orange-btn mb-20">Войти</a>
            <a href="/auth/register" class="black-btn">Создать аккаунт</a>
        </div>
    </div>
    @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')])
    <style>
        .horizontal-scrolling-news {
            display: flex;
            overflow-x: auto;
            padding: 30px;
            flex-wrap: nowrap;
            align-items: stretch;
        }
        .dots {
            text-align: center;
            margin-top: 10px;
        }

        .dots button {
            width: 10px;
            height: 10px;
            background: #ccc;
            border: none;
            border-radius: 50%;
            margin: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .dots button.active {
            background: #333;
        }
        @media (max-width: 600px) {
            .horizontal-scrolling-news .item {
                width: 190px !important;
                min-width: 190px !important;
            }
        }
    </style>
    <script>
        function showModalFav() {
            document.getElementById('modal-fav').style.display = 'block'
        }
        function hideModalFav() {
            document.getElementById('modal-fav').style.display = 'none'
        }
        const carousel = document.getElementById('carousel');
        const dotsContainer = document.getElementById('carousel-dots');
        const items = carousel.querySelectorAll('.item');

        const groupSize = 4; // сколько элементов в одной группе
        const totalGroups = Math.ceil(items.length / groupSize);

        for (let i = 0; i < totalGroups; i++) {
            const dot = document.createElement('button');
            dot.addEventListener('click', () => {
                const scrollTo = i * groupSize * items[0].offsetWidth;
                carousel.scrollTo({
                    left: scrollTo,
                    behavior: 'smooth'
                });
            });
            dotsContainer.appendChild(dot);
        }

        const dots = dotsContainer.querySelectorAll('button');

        // Обновление активной точки при прокрутке
        carousel.addEventListener('scroll', () => {
            const index = Math.round(carousel.scrollLeft / (groupSize * items[0].offsetWidth));
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[index]) dots[index].classList.add('active');
        });

        // Активировать первую точку по умолчанию
        if (dots.length) dots[0].classList.add('active');
    </script>
@endsection
