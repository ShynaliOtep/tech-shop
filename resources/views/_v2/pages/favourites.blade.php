@extends('_v2.layouts.base')
@section('content')
    <h4 class="big-white-title">Избранное</h4>
    @if(count($goods) != 0)
        @foreach($goods as $good)
            <div class="col s6 m4 l3">
                @include('_v2.components.good.goodCard', ['good' => $good])
            </div>
        @endforeach
    @else
        <h5 class="white-text center">{{__('translations.There is nothing here yet')}} :(</h5>
    @endif
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
        .good-card {
            width: 425px;
        }
        @media (max-width: 600px) {
            .good-card {
                width: 48%;
            }
        }
    </style>
@endsection
