@extends('_v2.layouts.base')
@section('content')
    <div>
        <bonus-page-new></bonus-page-new>
    </div>
    @vite('resources/js/app.js')
    @push('scripts')
        <script src="{{asset('js/select.js')}}"></script>
    @endpush
@endsection
