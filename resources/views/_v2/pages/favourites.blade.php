@extends('_v2.layouts.base')
@section('content')
    <h4 class="big-white-title">Избранное</h4>
    @if(count($goods) != 0)
        <div class="items">
            @foreach($goods as $good)
                <div class="item">
                    @include('_v2.components.good.goodCard', ['good' => $good])
                </div>
            @endforeach
        </div>
    @else
        <h5 class="white-m-text">{{__('translations.There is nothing here yet')}} :(</h5>
    @endif
    @push('scripts')
        <script src="{{asset('js/favoriteActions.js')}}"></script>
        <script src="{{asset('js/cart.js?v=4')}}"></script>
    @endpush
    @include('auth.modal', ['icon' => 'favorite_border', 'title' => __('translations.Authorization required'), 'content' => __('translations.To add a product to your favorites, you must be authenticated')])
    <style>

    </style>
@endsection
