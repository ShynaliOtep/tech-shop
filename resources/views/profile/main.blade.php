@php use App\Models\BonusTransaction; @endphp
@extends('app')
@section('content')
    <h5 class="white-text">Ваш профиль:</h5>

    <div class="profile-bonus-block">
        <p class="white-text">Ваш бонус: <span>{{$client->getBonus()->balance}}</span> тенге</p>
    </div>
    <div class="referral-block">
        <label class="white-text">Ваша реферальная ссылка:</label>
        <input style="color: #FFFFFF" type="text" id="referralLink" value="{{ $client->getReferralLink() }}" readonly>
        <button class="orange white-text darken-4 " style="border-radius: 16px; border: none; padding: 5px 10px; cursor: pointer" onclick="copyReferral()">Скопировать</button>
    </div>
    <br>
    <ul class="collection basic-info grey z-depth-5">
        <li class="collection-item grey darken-3 white-text">{{__('translations.Full name')}}: <span
                class="orange-text">{{$client->name}}</span>
        </li>
        <li class="collection-item grey darken-3 white-text">{{__('translations.Phone')}}: <span
                class="orange-text">{{$client->phone}}</span></li>
        <li class="collection-item grey darken-3 white-text">{{__('translations.Email')}}: <span
                class="orange-text">{{$client->email}}</span></li>
        <li class="collection-item grey darken-3 white-text">Instagram: <span
                class="orange-text">{{$client->instagram}}</span></li>
        <li class="collection-item grey darken-3 white-text">{{__('translations.Registration date')}}: <span
                class="orange-text">{{$client->created_at}}</span></li>
        <li class="collection-item grey darken-3 white-text">{{__('translations.Profile last update date')}}: <span
                class="orange-text">{{$client->updated_at}}</span></li>
        <li class="collection-item grey darken-3 white-text"><p>{{__('translations.Total orders amount')}}: <span
                    class="orange-text"><b>{{$client->order_amount}}</b></span></p></li>
        <li class="collection-item grey darken-3 white-text"><p>{{__('translations.Total good rent amount')}}: <span
                    class="orange-text"><b>{{$client->order_item_amount}}</b></span></p></li>
        <li class="collection-item grey darken-3 white-text"><p>{{__('translations.Discount')}}: <span
                    class="orange-text"><b>{{$client->discount}}%</b></span></p></li>
    </ul>
    @if($client->idCards())
        <h5 class="white-text">{{__('translations.Id card pics')}}: </h5>
        <div class="id-image-wrapper row">
            @foreach($client->idCards() as $idPicture)
                <img loading="lazy" class="materialboxed id-image z-depth-5" src="{{$idPicture->url}}">
            @endforeach
        </div>
        <hr>
    @endif
    @if($client->signature())
        <h5 class="white-text">{{__('translations.Signature file')}}: </h5>
        <div class="">
            <a href="{{$client->signature()->url}}"><p class="flow-text orange-text"><u>Перейдите по ссылке для
                        просмотра</u></p></a>
        </div>
    @endif

    @php
        $transactions = BonusTransaction::where('user_id',Auth::guard('clients')->id())
        ->orderByDesc('created_at')
        ->get();
        $availableBalance = $client->getAvailableBalance();
    @endphp

    <div>
        <h5 class="white-text">Транзакций бонусов</h5>
        <table class="bonus-transactions-profile collection basic-info grey z-depth-5">
            <tr style="border-color: #fff">
                <th>Сумма</th>
                <th>Тип</th>
                <th>Дата</th>
            </tr>
            @foreach ($transactions as $transaction)
                <tr style="border-color: #fff">
                    <td>{{ number_format($transaction->amount, 2) }} ₸</td>
                    <td>{{ $transaction->type == 'deposit' ? 'Поступление' : 'Снятие' }}</td>
                    <td>{{ $transaction->created_at->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
        </table>
    </div>

    @if ($availableBalance > 0)
        <form action="{{ route('withdraw.request') }}" method="POST">
            @csrf
            <input type="hidden" name="amount" value="{{ $availableBalance }}">
            <button class="orange white-text darken-4 " style="border-radius: 16px; border: none; padding: 5px 10px; cursor: pointer" type="submit">Снять {{ number_format($availableBalance, 2) }} ₸</button>
        </form>
    @else
        <p style="color: red">Недостаточно средств для вывода</p>
    @endif

    <script>
        function copyReferral() {
            let input = document.getElementById("referralLink");
            input.select();
            document.execCommand("copy");
            alert("Реферальная ссылка скопирована!");
        }
    </script>
@endsection
