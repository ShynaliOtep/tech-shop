<?php
?>


@extends('app')
@section('content')
    <div class="mobile-navbar">
        <div class="container">
            <hr>
            <li class="menu-item">
                <div class="select-city">
                    <div class="language-wrapper">
                        <a href="{{route('selectCity', 1)}}" class="btn language-btn white-text @if(session()->get('select_city') === 1) orange darken-4 @else grey darken-2 @endif">{{__('translations.Almaty')}}</a>
                        <a href="{{route('selectCity', 2)}}" class="btn language-btn white-text @if(session()->get('select_city') === 2) orange darken-4 @else grey darken-2 @endif">{{__('translations.Astana')}}</a>
                    </div>
                </div>
            </li>
            <li class="menu-item center">
                <div class="language-wrapper">
                    <a href="{{route('changeLang', 'en')}}" class="btn language-btn white-text @if(session()->get('locale') === 'en') orange darken-4 @else grey darken-2 @endif">{{__('translations.EN')}}</a>
                    <a href="{{route('changeLang', 'ru')}}" class="btn language-btn white-text @if(session()->get('locale') === 'ru') orange darken-4 @else grey darken-2 @endif">{{__('translations.RU')}}</a>
                </div>
                <hr>
                <div class="hide-on-med-and-up search-wrapper valign-wrapper hide-on-med-and-up input-field">
                    <input id="search" type="text"
                           class="validate browser-default text-white center-align autocomplete"
                           placeholder="{{__('translations.Search')}}">
                    <i class="material-icons white-text">
                        search
                    </i>
                </div>
                <hr class="hide-on-med-and-up">
            </li>
            <li class="menu-item center">
                    <form action="/select-city" method="POST">
                        @php
                        $cities = \App\Models\City::get();
                        @endphp
                        @csrf
                        <select name="city_id" onchange="this.form.submit()">
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ session('selected_city') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
            </li>
            @foreach($goodTypes as $goodType)
                <li class="menu-item">
                    <a href="{{route('goodList', $goodType->code, false)}}"
                       class="white-text waves-effect waves-light menu-item-link waves-ripple">
                    <span class="menu-item-content">
                        @if(Request::is('category/' . $goodType->code))
                            <span class="btn-medium btn-floating orange darken-4">
                                <i class="material-icons">{{$goodType->icon}}</i>
                            </span>
                            <span class="orange-text">{{__( 'translations.'. $goodType->code)}}</span>
                        @else
                            <span class="btn-medium btn-floating grey darken-4">
                                <i class="material-icons">{{$goodType->icon}}</i>
                            </span>
                            <span class="">{{__( 'translations.'. $goodType->code)}}</span>
                        @endif
                    </span>
                    </a>
                </li>
            @endforeach
        </div>
    </div>
    <style>
        .mobile-navbar {
            margin-bottom: 30px;
        }
        footer {
            display: none !important;
        }
    </style>

@endsection

