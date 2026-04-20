<?php
/**
 * @var string $code
 * @var $icon
 */
?>
<a href="{{route('goodList', $goodType->code, false)}}" class="menu-item
@if(Request::is('category/' . $goodType->code))  menu-item-active @endif
">
    {!!$icon!!}
{{--    <img class="menu-icon" src="{{$icon}}" alt="">--}}
   <span class="menu-text">{{__($name)}}</span>
</a>

<style>
    .menu-item {
        text-decoration: none;
        display: flex;
        padding: 15px 0;
        color: #404040;
        align-content: center;
    }
    .menu-item:hover {
        color: #ffffff !important;
    }
    .menu-item svg {
        width: 30px;
        margin-right: 20px;
    }
    .menu-text {
        font-size: 24px;
        font-weight: 500;
        text-decoration: none;
    }
</style>
