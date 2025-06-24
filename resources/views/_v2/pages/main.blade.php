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
                    <h5 class="white-text center">{{__('translations.There is nothing here yet')}} :(</h5>
                @endif
            </div>
        @endforeach
    @endisset
    @push('scripts')
        <script src="{{asset('js/favoriteActions.js')}}"></script>
        <script src="{{asset('js/cart.js')}}"></script>
    @endpush
    @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')])
    <style>
        .horizontal-scrolling-news {
            display: flex;
            overflow-x: auto;
            padding: 30px;
            flex-wrap: nowrap;
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
    </style>
    <script>
        const carousel = document.getElementById('carousel');
        const dotsContainer = document.getElementById('carousel-dots');
        const items = carousel.querySelectorAll('.item');

        items.forEach((_, index) => {
            const dot = document.createElement('button');
            dot.addEventListener('click', () => {
                carousel.scrollTo({
                    left: index * items[0].offsetWidth,
                    behavior: 'smooth'
                });
            });
            dotsContainer.appendChild(dot);
        });

        const dots = dotsContainer.querySelectorAll('button');

        // Активная точка при скролле
        carousel.addEventListener('scroll', () => {
            const index = Math.round(carousel.scrollLeft / items[0].offsetWidth);
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[index]) dots[index].classList.add('active');
        });

        // Установить первую точку как активную
        dots[0].classList.add('active');
    </script>
@endsection
