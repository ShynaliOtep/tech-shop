@extends('_v2.layouts.base')
@section('content')
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
            padding: 10px;
        }
    </style>
@endsection
