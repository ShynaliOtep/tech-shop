@extends('_v2.layouts.base')
@section('content')
    <div class="good-types">
        @foreach($goodTypes as $goodType)
            @include(
                '_v2.components.menu.menu-item',
                [
                    'code' => $goodType->code,
                    'icon' => '/img/types/' . $goodType->icon . '.svg'
                ]
            )
        @endforeach
    </div>
    <style>
        .base-section {
            background-color: #191919 !important;
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
        .dropdown {
            background-color: #151515;
        }
        .dropdown-content {

        }
    </style>
@endsection
