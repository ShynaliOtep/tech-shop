@extends('_v2.layouts.base')
@section('content')
    @if(isset($orders) && count($orders) > 0)
        <h5 class="big-white-title page-presenter-header">{{__('translations.Your orders')}}</h5>
        <div class="row">
            @foreach($orders as $order)
                <div class="col s12 order-wrapper mb-20">
                    <p class="white-m-text mb-10 order-title">{{__('translations.Order')}} <span class="orange-text"><u> #{{$order->id}}</u></span></p>
                    <p class="white-s-text mb-20">Дата оформление {{\App\Services\Date\DatetimeService::textFormat(new DateTime($order->created_at))}}</p>
                    <div class="order-main-block">
                        <img src="{{\App\Services\Order\OrderService::getFirstImage($order)}}" alt="">
                        <div class="order-text-block">
                            <p class="order-text white-m-text mb-10">
                                Заказ: {{\App\Services\Order\OrderService::getOrderText($order, 100)}}
                            </p>
                            <a href="{{route('viewOrder', $order)}}">подробнее</a>
                        </div>
                    </div>
                    @php
                    $color = '#007bff';
                    if ($order->status == 'waiting') {
                        $width = (100 / 4) * 0;
                    } elseif ($order->status == 'confirmed') {
                        $width = (100 / 4) * 2;
                    } elseif ($order->status == 'in_rent') {
                        $width = (100 / 4) * 3;
                    } elseif ($order->status == 'returned') {
                        $width = 100;
                    } else {
                        $width = (100 / 4) * 1;
                        $color = 'red';
                    }
                    @endphp
                    <div class="progress-container">
                        <!-- Линия до центра второй точки (25% + половина между точками = 33.33%) -->
                        <div class="progress-fill" style="width: {{$width}}%; background-color: {{$color}}"></div>
                        <div class="step @if($order->status == 'waiting') active @endif"></div>
                        <div class="step @if($order->status == 'cancelled') cancel @endif"></div>
                        <div class="step @if($order->status == 'confirmed') active @endif"></div>
                        <div class="step @if($order->status == 'in_rent') active @endif"></div>
                        <div class="step @if($order->status == 'returned') active @endif"></div>
                    </div>
                    <div class="status-label" style="margin-left: calc(100px + {{$width/2}}%)">{{__('translations.' . $order->status)}}</div>
                    <br>
{{--                    <p class="white-text">{{__('translations.Amount of goods')}}: <span class="orange-text">{{count($order->orderItems)}}</span></p>--}}
{{--                    <p class="white-text">{{__('translations.Order status')}}: <span class="orange-text">{{__('translations.' . $order->status)}}</span></p>--}}
{{--                    <p class="white-text">{{__('translations.Total sum')}}: <span class="orange-text">{{$order->amount_paid}} тг</span></p>--}}
                    <hr>
                </div>
            @endforeach
        @php
            $showModal = request()->has('new_order');
        @endphp
        @if($showModal)
            <div style="z-index: 1003; display: block; opacity: 1; bottom: 0; transform: scaleX(1) scaleY(1);"
                id="modal_new_order" class="modal" onclick="hideModalNewOrder()">
                <div class="black-block simple-centred-block modal-block ">
            <span class="close" onclick="hideModalNewOrder()">
               <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
                </svg>
            </span>
                    <p class="modal-title big-white-title mb-20">
                        Ваш заказ был успешно оформлен!
                    </p>
                    <p class="grey-s-light-text mb-20">
                        Спасибо, что выбираете нас!
                    </p>
                    <p   class="black-btn mb-20" onclick="hideModalNewOrder()">Закрыть</p>
                    {{--          --}}
                    {{--                <a href="/auth/register" class="black-btn">Создать аккаунт</a>--}}
                </div>
            </div>
                @endif
{{--            @push('scripts')--}}
{{--                <script>--}}
{{--                    document.addEventListener('DOMContentLoaded', function () {--}}
{{--                        // Инициализируем все модалки--}}
{{--                        const modals = document.querySelectorAll('.modal');--}}
{{--                        M.Modal.init(modals);--}}

{{--                        // Получаем query-параметры--}}
{{--                        const params = new URLSearchParams(window.location.search);--}}

{{--                        if (params.has('new_order')) {--}}
{{--                            const modalEl = document.getElementById('success-order');--}}
{{--                            const instance = M.Modal.getInstance(modalEl);--}}
{{--                            if (instance) {--}}
{{--                                instance.open();--}}
{{--                            }--}}
{{--                        }--}}
{{--                    });--}}
{{--                    function hideModalNewOrder() {--}}
{{--                        document.getElementById('modal_new_order').style.display = 'none'--}}
{{--                    }--}}
{{--                </script>--}}
{{--            @endpush--}}
        </div>

    @else
        <h5 class="white-m-text">{{__('translations.You do not have any orders yet')}}</h5>
    @endif
    <script>
        function hideModalNewOrder() {
            document.getElementById('modal_new_order').style.display = 'none'
        }
    </script>
@endsection
<style>
    #success-order {

    }
    .status-label {
        display: block;
        font-weight: 500;
        color: #007bff;
        margin-top: 0;
    }
    .progress-container {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 90%;
        margin: 20px auto;
    }

    .progress-container::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 2px;
        background-color: #ccc;
        transform: translateY(-50%);
        z-index: 1;
    }

    .progress-fill {
        position: absolute;
        top: 50%;
        left: 0;
        height: 4px;
        transform: translateY(-50%);
        z-index: 2;
    }

    .step {
        width: 10px;
        height: 10px;
        background-color: #ccc;
        border-radius: 50%;
        z-index: 3;
        opacity: 0;
    }

    .step.completed,
    .step.active {
        opacity: 1;
        background-color: #007bff;
    }
    .step.cancel {
        opacity: 1;
        background-color: red;
    }
</style>
