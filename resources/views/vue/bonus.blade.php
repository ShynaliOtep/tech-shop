@extends('app')
@section('content')
    <div>
        <bonus-page></bonus-page>
    </div>
    @vite('resources/js/app.ts')
    @push('scripts')
        {{--        <script src="{{asset('js/cartActions.js')}}"></script>--}}
        <script src="{{asset('js/select.js')}}"></script>
        {{--        <script src="{{asset('js/cart.js')}}"></script>--}}
    @endpush
@endsection
