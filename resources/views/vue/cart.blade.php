@extends('app')
@section('content')
    <div>
        <cart-page></cart-page>
    </div>
{{--    <script type="module" src="{{ mix('resources/js/app.js') }}"></script>--}}
    @vite('resources/js/app.js')
    @push('scripts')
        {{--        <script src="{{asset('js/cartActions.js')}}"></script>--}}
        <script src="{{asset('js/select.js')}}"></script>
{{--        <script src="{{asset('js/cart.js')}}"></script>--}}
    @endpush
@endsection
