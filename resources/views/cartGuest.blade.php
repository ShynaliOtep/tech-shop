<div id="{{$modalClass}}" class="modal bottom-sheet custom-modal">
    <div class="modal-content container center">
        <h4>{{$title}}</h4>
        <p>{{$subTitle}}</p>
        <p><a href="{{$link}}">{{$linkCaption}}</a></p>
        <p>{{$downTitle}}</p>
    </div>
    <div class="divider"></div>
    <div class="modal-footer">
        <div class="row center">
            <p><b class="error-text red-text"></b></p>
            <button class="btn-large nav-link orange darken-4 auth-link confirm-order-btn">
                {{$btnCaption}}
                <i class="material-icons">done</i>
            </button>
        </div>
    </div>
    @push('scripts')
        <script>
            document.querySelector('.confirm-order-btn').onclick = e =>{
                {{$btnAction}}(e)
            }
        </script>
    @endpush
</div>
