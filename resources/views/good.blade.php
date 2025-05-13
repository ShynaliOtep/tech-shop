@extends('app')
@section('content')
    @if(isset($carousel))
        <div>
            <carousel></carousel>
        </div>
        @vite('resources/js/app.js')
    @endif
    @if(isset($news))
        <div class="row">
            <h5 class="white-text page-presenter-header">{{__('translations.news block')}}</h5>
            <div class="horizontal-scrolling-news">

                    @foreach($news as $good)
                        <div class="col s6 m4 l3 new-item">
                            @include('goodCard', ['good' => $good, 'isNew' => true])
                        </div>
                    @endforeach
            </div>
        </div>
    @endif
    @isset($viewedGoodTypes)
        @foreach($viewedGoodTypes as $goodType)
    <div class="row">
            <h5 class="white-text page-presenter-header">{{__('translations.' . $goodType->code)}}</h5>
            @if(count($goodType->goods) != 0)
                @foreach($goodType->goods as $good)
                    <div class="col s6 m4 l3">
                        @include('goodCard', ['good' => $good])
                    </div>
                @endforeach
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
            padding: 10px;
        }
    </style>
@endsection
