@extends('app')
@section('content')
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
@endsection
