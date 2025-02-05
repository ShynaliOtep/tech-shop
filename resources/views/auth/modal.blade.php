<div id="auth-modal" class="modal">
    <div class="modal-content container center">
        <h1 class="btn-floating btn-large orange darken-4"><i
                class="large material-icons text-accent-4 white-text ">{{$icon}}</i></h1>
        <h4>{{$title}}</h4>
        <p>{{$content}}</p>
    </div>
    <div class="divider"></div>
    <div class="modal-footer">
        <div class="row">
            <div class="col s12 center">
                <a href="{{route('login')}}" class="btn-large nav-link orange darken-4 auth-link ">
                    {{__('translations.Log in')}}
                </a>
            </div>
            <div class="col s12 center">
                <a href="{{route('register')}}" class="btn-large modal-btn grey black white-text register-link">{{__('translations.Register')}}</a>
            </div>
        </div>
    </div>
</div>
