@extends('app')
@section('content')
    <h5 class="white-text page-presenter-header">Редактирование профиля:</h5>
    <form method="POST" action="{{route('updateProfile')}}" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="container profile-form-wrapper z-depth-5">
            <div class="input-field">
                <i class="material-icons prefix white-text">account_circle</i>
                <input name="name" id="name" value={{$client->name}} type="text" placeholder="{{__('translations.Full name')}}" class="white-text" required>
            </div>
            <div class="input-field">
                <i class="material-icons prefix white-text">phone</i>
                <input name="phone" id="phone" value={{$client->phone}} type="tel" placeholder="{{__('translations.Phone')}}" class="white-text" required>
            </div>
            <div class="input-field">
                <i class="material-icons prefix white-text">email</i>
                <input name="email" id="email" value={{$client->email}} type="tel" placeholder="{{__('translations.Email')}}" class="white-text" required>
            </div>
            <div class="input-field">
                <i class="material-icons prefix white-text">
                    <img loading="lazy" src="{{asset('/img/instagram.svg')}}" height="35px" alt="">
                </i>
                <input name="instagram" id="instagram"
                       value={{$client->instagram}} type="text" placeholder="{{__('translations.Your instagram')}}" class="white-text" required>
            </div>
            <div class="file-field input-field">
                <div>
                    <input type="file" name="files[]" multiple="multiple" accept=".jpg,.jpeg,.png">
                </div>
                <div class="file-path-wrapper">
                    <i class="material-icons prefix white-text">attach_file</i>
                    <input id="file-path" class="file-path" type="text"
                           placeholder="{{__('translations.Client id card help')}}">
                </div>
            </div>
            <div class="file-field input-field">
                <div>
                    <input type="file" name="signature" multiple="multiple" accept=".pdf">
                </div>
                <div class="file-path-wrapper">
                    <i class="material-icons prefix white-text">assignment</i>
                    <input id="file-path" class="file-path" type="text"
                           placeholder="{{__('translations.Client signature help')}}">
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-medium save-profile-btn orange darken-4 white-text">{{__('translations.Save')}} <i class="material-icons">save</i></button>
        </div>
        @if ($errors->any())
            <div class="col s12">
                <ul class="red-text">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
@endsection
