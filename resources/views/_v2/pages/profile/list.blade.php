@extends('_v2.layouts.base')
@section('content')
    <div class="profile-list">
        <a href="{{route('getMyOrders')}}" class="profile-list-item">{{__('translations.My orders')}}</a>
        <a href="{{route('viewProfile')}}" class="profile-list-item">{{__('translations.Check profile')}}</a>
        <a href="/bonus" class="profile-list-item">Скидки</a>
        <div class="profile-break"></div>
        <a href="{{route('logout')}}" class="profile-list-item">{{__('translations.Logout')}}</a>
    </div>
    <style>
        .base-section {
            background-color: #151515 !important;
        }
        .content {
            padding: 10px 30px !important;
        }
        footer {
            display: none !important;
        }
        .good-types {
            margin-top: 0 !important;
        }
        .dropbtn {
            background-color: #151515;
        }
        .dropdown-content {

        }
        .profile-list {
            margin-top: 30px;
            width: 100%;
        }
        .profile-list-item {
            width: 100%;
            display: block;
            font-size: 24px;
            font-weight: 500;
            text-align: right;
            color: #404040;
            margin-bottom: 30px;
            text-decoration: none;
        }
        .profile-break {
            height: 30px;
        }
    </style>
@endsection
