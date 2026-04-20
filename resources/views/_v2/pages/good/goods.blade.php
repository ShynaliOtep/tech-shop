@extends('_v2.layouts.base')
@section('content')
    @isset($viewedGoodTypes)
        @foreach($viewedGoodTypes as $goodType)
            <div class="row">
                <h5 class="page-type-title">{{__($goodType->name)}}</h5>
                    <div style="position: relative">
                        <div class="dropdown-sort drrrr"  onclick="sortDropdownToggle()">

                            <div class="dropbtn-sort drrrr">

                                <svg class="sort-btn" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 2L7.64645 1.64645L8 1.29289L8.35355 1.64645L8 2ZM8.5 17C8.5 17.2761 8.27614 17.5 8 17.5C7.72386 17.5 7.5 17.2761 7.5 17L8.5 17ZM3.64645 5.64645L7.64645 1.64645L8.35355 2.35355L4.35355 6.35355L3.64645 5.64645ZM8.35355 1.64645L12.3536 5.64645L11.6464 6.35355L7.64645 2.35355L8.35355 1.64645ZM8.5 2L8.5 17L7.5 17L7.5 2L8.5 2Z" fill="#222222"></path> <path d="M16 22L15.6464 22.3536L16 22.7071L16.3536 22.3536L16 22ZM16.5 7C16.5 6.72386 16.2761 6.5 16 6.5C15.7239 6.5 15.5 6.72386 15.5 7L16.5 7ZM11.6464 18.3536L15.6464 22.3536L16.3536 21.6464L12.3536 17.6464L11.6464 18.3536ZM16.3536 22.3536L20.3536 18.3536L19.6464 17.6464L15.6464 21.6464L16.3536 22.3536ZM16.5 22L16.5 7L15.5 7L15.5 22L16.5 22Z" fill="#222222"></path> </g></svg>
                                <span  class="drrrr dropbtn-sort-text">
                            {{ request('sort') === 'cheap' ? 'Сначала дешевые' : (request('sort') === 'expensive' ? 'Сначала дорогие' : (request('sort') === 'popular' ? 'Популярные' : 'Новинки')) }}
                        </span>
                            </div>

                            <div id="sortDropdown" class="dropdown-content">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}"
                                   class="dropbtn-sort-text {{ request('sort') === null ? 'dropbtn-sort-text-active' : '' }}">
                                    Новинки
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'cheap']) }}"
                                   class="dropbtn-sort-text {{ request('sort') === 'cheap' ? 'dropbtn-sort-text-active' : '' }}">
                                    Сначала дешевые
                                </a>

                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'expensive']) }}"
                                   class="dropbtn-sort-text {{ request('sort') === 'expensive' ? 'dropbtn-sort-text-active' : '' }}">
                                    Сначала дорогие
                                </a>

                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}"
                                   class="dropbtn-sort-text {{ request('sort') === 'popular' ? 'dropbtn-sort-text-active' : '' }}">
                                    Популярные
                                </a>
                            </div>

                        </div>
                    </div>

                <div class="sub-types">
                    @foreach($subTypes as $subType)
                         @php
                            $currentTypes = request('types', []);
                            if (in_array($subType->id, $currentTypes)) {
                                $newTypes = array_values(array_diff($currentTypes, [$subType->id]));
                                  $added = true;
                            } else {
                                $newTypes = array_merge($currentTypes, [$subType->id]);
                                $added = false;
                            }
                        @endphp

                        <a href="{{ request()->fullUrlWithQuery(['types' => $newTypes]) }}" class="btn-sub-type @if($added) btn-sub-type-added @endif">
                            {{ __($subType->name) }}
                            @if($added)
                                <svg class="sub-type-added-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 50">
                                    <path d="M 7.71875 6.28125 L 6.28125 7.71875 L 23.5625 25 L 6.28125 42.28125 L 7.71875 43.71875 L 25 26.4375 L 42.28125 43.71875 L 43.71875 42.28125 L 26.4375 25 L 43.71875 7.71875 L 42.28125 6.28125 L 25 23.5625 Z"></path>
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
                @if(count($goods) != 0)
                    <div class="items">
                        @foreach($goods as $good)
                            <div class="item">
                                @include('_v2.components.good.goodCard', ['good' => $good])
                            </div>
                        @endforeach
                    </div>
                @else
                    <h5 class="white-m-text mb-20" style="display: block; margin: 0 auto; text-align: center">{{__('translations.There is nothing here yet')}} :(</h5>
                @endif
            </div>
        @endforeach
    @endisset
    @push('scripts')
        <script src="{{asset('js/favoriteActions.js')}}"></script>
        <script src="{{asset('js/cart.js?v=4')}}"></script>
        <script>
            function sortDropdownToggle() {
                document.getElementById("sortDropdown").classList.toggle("show");
                document.getElementById("sortIcon").classList.toggle("show-icon");
            }

            window.addEventListener('click', function(event) {
                if (!event.target.closest('.drrrr')) {
                    document.getElementById("sortDropdown")?.classList.remove("show");
                    document.getElementById("sortIcon")?.classList.remove("show-icon");
                }
            });
        </script>
    @endpush
    @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')])
    <style>
        .horizontal-scrolling-news {
            display: flex;
            overflow-x: auto;
            padding: 10px;
        }
        .sub-types {
            display: flex;
            margin-bottom: 50px;
            margin-top: 70px;
            gap: 10px;
        }
        .btn-sub-type{
            color: #fff;
            border-radius: 20px;
            text-decoration: none;
            display: flex;
            font-weight: 400;
            font-size: 14px;
            justify-content: space-between;
            cursor: pointer;
            background-color: #191919;
            align-items: center;
            padding: 10px 15px;
        }
        .btn-sub-type-added {
            background: #FF962E;
        }
        .sub-type-added-icon {
            fill: white;
            width: 14px;
            margin-left: 10px;
        }
        .sort-btn {
            color: #fff;
            border-radius: 20px;
            text-decoration: none;
            display: flex;
            font-weight: 400;
            width: 15px;
            justify-content: space-between;
            cursor: pointer;
            background-color: #191919;
            align-items: center;
        }
        .dropdown-sort {
            width: 210px;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #191919;
            border-radius: 30px;
            padding: 15px 25px;
            cursor: pointer;
            z-index: 15;
        }
        .dropbtn-sort {
            display: flex;
            gap: 15px;
        }
        .dropbtn-sort-text {
            font-size: 14px;
            color: #ffffff !important;
        }
        .dropbtn-sort-text-active {
            color: #B3B3B3B2 !important;
        }
        #sortDropdown {
            margin-left: 0 !important;
        }
    </style>
@endsection
