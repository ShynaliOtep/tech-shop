@extends('_v2.layouts.base')
@section('content')
    <input type="hidden" name="" id="order-id-holder" value="{{$order->id}}">
    <h5 class="big-white-title page-presenter-header">Заказ <span class="orange-text">#{{$order->id}}</span></h5>
    <div class="row">
        <div class="col s12 order-view-wrapper z-depth-5 mb-20">
            @if(count($order->orderItems) > 0)
                <h5 class="white-m-text">
                    {{__('translations.Your order consists of the following goods')}}:
                </h5>
                @foreach($order->orderItems as $index => $item)
                    @php
                    if (isset($additionals)) {
                         $additionals = $item->additionals;
                    if (is_string($additionals)) {
                        $additionals = json_decode($item->additionals, true);
                    }
                    }
                    @endphp
                    <div class="order-v-item">
                            <div class="ov-texts">
                                <p class="white-s-text mb-10">{{$index + 1}}. {{__('translations.Name')}}: <span class="orange-text">{{$item->item->good['name_' . session()->get('locale', 'ru')]}}</span></p>
                                <p class="white-s-text mb-10">{{__('translations.Good cost')}}: <span class="orange-text">{{$item->item->good->discount_cost ?? $item->item->good->cost}}</span></p>
                                <p class="white-s-text mb-10">{{__('translations.Status')}}: <span class="orange-text">{{__('translations.'. $item->status)}}</span></p>
                                <p class="white-s-text mb-10">{{__('translations.Good type')}}: <a href="{{route('goodList', $item->item->good->goodType->code)}}" class="orange-text">{{$item->item->good->goodType->name}}</a></p>
                                <p class="white-s-text mb-10">{{__('translations.Rent start time')}}: <span class="orange-text">{{$item->rent_start_date}} {{$item->rent_start_time}}</span></p>
                                <p class="white-s-text mb-10">{{__('translations.Rent end time')}}: <span class="orange-text">{{$item->rent_end_date}} {{$item->rent_end_time}}</span></p>
                                @if (isset($additionals) &&  count($additionals) > 0)
                                    <p class="white-m-text mb-10">Дополнительные товары:</p>
                                    <ul>
                                        @foreach(\App\Models\Good::query()->whereIn('id',$additionals)->get() as $additional)
                                            <li class="white-s-text">{{$additional['name_' . session()->get('locale', 'ru')]}} <span class="grey-text"> (+{{($additional->additional_cost ?? $additional->cost) * $item->amount_of_days}} тг)</span></li>
                                        @endforeach
                                    </ul>
                                @endif
                                <p class="white-m-text mb-10">Общее количество дней: <span class="orange-text">{{$item->amount_of_days}}</span></p>
                                <hr class="mb-10">
                                <h5 class="white-m-text mb-20">Сумма к оплате: <span class="orange-text">{{$item->amount_paid}}</span></h5>
                            </div>
                            <div class="ov-image">
                                <a href="{{route('viewGood', $item->item->good)}}">
                                    @if($item->item->good->attachment()?->first()?->url())
                                        <img loading="lazy" src="{{$item->item->good->attachment->first()->url}}" alt="" class="order-item-image-wrapper z-depth-5">
                                    @else
                                        <img loading="lazy" src="{{asset('img/no-image.jpg')}}" class="materialboxed good-image z-depth-5" width="100%">
                                    @endif
                                </a>
                            </div>
                    </div>
                @endforeach
            @endif
        </div>
        @if ($order->orderItems()->where('status', '=', 'in_rent')->count() === 0)
            <div class="center">
                <a href="#order-canceling-modal" class="orange-btn mb-20 mini-btn">Отменить заказ</a>
            </div>
        @endif
        @include('confirmModal', [
            'modalClass' => 'order-canceling-modal',
            'title' => __('translations.Are you sure you want to cancel the order?'),
            'subTitle' => null,
            'link' => null,
            'linkCaption' => null,
            'downTitle' => __('translations.After the cancellation this order will be no longer available'),
            'btnAction' => 'cancelOrder',
            'btnCaption' => __('translations.Cancel order'),
        ])
    </div>
    @push('scripts')
        <script src="{{asset('js/orderActions.js')}}"></script>
    @endpush
@endsection
