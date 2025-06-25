@extends('_v2.layouts.base')
@section('content')
    <div class="container center">
        <h4 class="big-white-title">{{__('translations.You have already placed an order in the past 15 seconds!')}}</h4>
        <h5 class="white-s-text">{{__('translations.Try again after few seconds!')}}</h5>
    </div>
@endsection
