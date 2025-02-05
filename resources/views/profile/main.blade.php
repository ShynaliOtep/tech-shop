@extends('app')
@section('content')
    <h5 class="white-text">Ваш профиль:</h5>
    <a href="{{route('editProfile')}}" class="btn orange darken-4 white-text">{{__('translations.Edit')}} <i class="material-icons">create</i></a>
    <ul class="collection basic-info grey z-depth-5">
        <li class="collection-item grey darken-3 white-text">{{__('translations.Full name')}}: <span class="orange-text">{{$client->name}}</span>
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
            <a href="{{$client->signature()->url}}"><p class="flow-text orange-text"><u>Перейдите по ссылке для просмотра</u></p></a>
        </div>
    @endif
@endsection
