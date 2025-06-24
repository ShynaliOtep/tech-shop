@extends('_v2.layouts.base')
@section('content')
    <div>
        <cart-page2></cart-page2>
    </div>
    @vite('resources/js/app.js')
    @push('scripts')
        <script src="{{asset('js/select.js')}}"></script>
    @endpush
@endsection
