@extends('_v2.layouts.base')
@section('content')
    <script>
        window.PIXEL_CITY_ID = {{ (int) \App\Services\City\CityService::city() }};
    </script>
    <div>
        <cart-page2></cart-page2>
    </div>
    @vite('resources/js/app.js')
    @push('scripts')
        <script src="{{asset('js/select.js')}}"></script>
    @endpush
@endsection
