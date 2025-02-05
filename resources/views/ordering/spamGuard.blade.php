@extends('app')
@section('content')
    <div class="container center">
        <h4 class="white-text">{{__('translations.You have already placed an order in the past 15 seconds!')}}</h4>
        <h5 class="white-text">{{__('translations.Try again after few seconds!')}}</h5>
    </div>
@endsection
