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
            <div class="h-carousel">
            @include('_v2.components.carousel-arrows')
            <div class="items horizontal-scrolling-news">
                @php
                    // Бейджи наличия на главной: одним расчётом для всех карточек.
                    \App\Services\Good\GoodAvailability::warm(
                        collect($news)
                            ->merge(collect($sets)->pluck('good'))
                            ->merge(collect($viewedGoodTypes ?? [])->flatMap(fn ($type) => $type->goods))
                            ->filter()
                    );
                @endphp
                @foreach($news as $good)
                    <div class="item">
                        @include('_v2.components.good.goodCard', ['good' => $good, 'isNew' => true])
                    </div>
                @endforeach
            </div>
            <div class="dots"></div>
            </div>
        </div>
    @endif

    @if(isset($sets))
        <div>
            <h5 class="page-type-title">{{__('Наборы')}}</h5>
            <div class="h-carousel">
            @include('_v2.components.carousel-arrows')
            <div class="items horizontal-scrolling-news">
                @foreach($sets as $set)
                    <div class="item">
                        @include('_v2.components.good.goodCard', ['good' => $set->good])
                    </div>
                @endforeach
            </div>
            <div class="dots"></div>
            </div>
        </div>
    @endif

    @isset($viewedGoodTypes)
        @foreach($viewedGoodTypes as $goodType)
            <div class="row">
                <h5 class="page-type-title">{{__($goodType->name)}}</h5>
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
        <script src="{{asset('js/cart.js?v=4')}}"></script>
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
        .h-carousel {
            position: relative;
        }
        /* Слева отступ = промежутку между карточками: лента NEW (left: -15px) помещается,
           а справа отступа нет — лента следующей карточки не торчит из-за края. */
        .horizontal-scrolling-news {
            display: flex;
            overflow-x: auto;
            padding: 30px 0 30px 16px;
            scroll-padding-left: 16px;
            scroll-snap-type: x mandatory;
            flex-wrap: nowrap;
            align-items: stretch;
            scrollbar-width: none;         /* Firefox */
            -ms-overflow-style: none;
        }
        .horizontal-scrolling-news .item {
            flex: 0 0 calc((100% - 32px) / 3);
            min-width: 0;
            scroll-snap-align: start;
        }
        .horizontal-scrolling-news.is-dragging {
            scroll-snap-type: none;
            cursor: grabbing;
        }
        .horizontal-scrolling-news.is-dragging * {
            user-select: none;
        }
        .h-carousel-btn {
            position: absolute;
            top: 50%;
            z-index: 5;
            width: 48px;
            height: 48px;
            margin-top: -24px;
            border: none;
            border-radius: 50%;
            background-color: var(--surface-hover);
            box-shadow: inset 0 0 0 1px var(--line-soft), var(--shadow-card);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s, opacity 0.2s;
        }
        .h-carousel-btn:hover {
            background-color: var(--accent);
        }
        .h-carousel-btn[disabled] {
            opacity: 0;
            pointer-events: none;
        }
        .h-carousel-prev {
            left: -20px;
        }
        .h-carousel-next {
            right: -20px;
        }
        .horizontal-scrolling-news::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        .dots {
            text-align: center;
            margin-top: 10px;
        }

        .dots button {
            width: 8px;
            height: 8px;
            background: var(--line);
            border: none;
            border-radius: 8px;
            margin: 5px;
            cursor: pointer;
            transition: background 0.3s, width 0.3s;
        }

        .dots button.active {
            width: 24px;
            background: var(--accent);
        }
        @media (max-width: 992px) {
            .horizontal-scrolling-news .item {
                flex: 0 0 calc((100% - 16px) / 2);
            }
        }
        @media (max-width: 600px) {
            .horizontal-scrolling-news .item {
                width: 190px !important;
                min-width: 190px !important;
            }
            .horizontal-scrolling-news {
                padding: 20px 0 0;
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
        // Горизонтальные карусели: стрелки, точки по страницам и перетаскивание мышью.
        // Колёсиком без Shift такой блок не прокрутить, поэтому на компьютере нужны стрелки.
        document.querySelectorAll('.h-carousel').forEach(function (root) {
            const track = root.querySelector('.horizontal-scrolling-news');
            const items = track.querySelectorAll('.item');
            const prev = root.querySelector('.h-carousel-prev');
            const next = root.querySelector('.h-carousel-next');
            const dotsContainer = root.querySelector('.dots');
            if (!items.length) return;

            function itemStep() {
                const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
                return items[0].offsetWidth + gap;
            }
            function perPage() {
                return Math.max(1, Math.floor(track.clientWidth / itemStep()));
            }
            function pageStep() {
                return perPage() * itemStep();
            }

            function renderDots() {
                dotsContainer.innerHTML = '';
                const pages = Math.ceil(items.length / perPage());
                if (pages < 2) return;
                for (let i = 0; i < pages; i++) {
                    const dot = document.createElement('button');
                    dot.type = 'button';
                    dot.setAttribute('aria-label', String(i + 1));
                    dot.addEventListener('click', function () {
                        track.scrollTo({left: i * pageStep(), behavior: 'smooth'});
                    });
                    dotsContainer.appendChild(dot);
                }
            }

            function update() {
                const max = track.scrollWidth - track.clientWidth - 2;
                if (prev) prev.disabled = track.scrollLeft <= 2;
                if (next) next.disabled = track.scrollLeft >= max;
                const dots = dotsContainer.querySelectorAll('button');
                const index = track.scrollLeft >= max
                    ? dots.length - 1
                    : Math.round(track.scrollLeft / pageStep());
                dots.forEach(function (dot, i) {
                    dot.classList.toggle('active', i === index);
                });
            }

            if (prev) prev.addEventListener('click', function () {
                track.scrollBy({left: -pageStep(), behavior: 'smooth'});
            });
            if (next) next.addEventListener('click', function () {
                track.scrollBy({left: pageStep(), behavior: 'smooth'});
            });

            // Перетаскивание мышью; на телефоне листается пальцем само.
            let startX = 0, startScroll = 0, dragging = false, moved = false;
            track.addEventListener('pointerdown', function (e) {
                if (e.pointerType !== 'mouse' || e.button !== 0) return;
                dragging = true;
                moved = false;
                startX = e.clientX;
                startScroll = track.scrollLeft;
            });
            window.addEventListener('pointermove', function (e) {
                if (!dragging) return;
                const dx = e.clientX - startX;
                if (!moved && Math.abs(dx) > 5) {
                    moved = true;
                    track.classList.add('is-dragging');
                }
                if (moved) track.scrollLeft = startScroll - dx;
            });
            window.addEventListener('pointerup', function () {
                if (!dragging) return;
                dragging = false;
                if (!moved) return;
                track.classList.remove('is-dragging');
                const step = itemStep();
                track.scrollTo({left: Math.round(track.scrollLeft / step) * step, behavior: 'smooth'});
            });
            // Карточки — ссылки: после перетаскивания клик не должен открывать товар.
            track.addEventListener('click', function (e) {
                if (moved) {
                    e.preventDefault();
                    e.stopPropagation();
                    moved = false;
                }
            }, true);
            track.addEventListener('dragstart', function (e) {
                e.preventDefault();
            });

            track.addEventListener('scroll', update, {passive: true});
            window.addEventListener('resize', function () {
                renderDots();
                update();
            });
            renderDots();
            update();
        });
    </script>
@endsection
