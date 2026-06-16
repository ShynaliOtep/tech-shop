<div id="{{$modalClass}}" class="modal" style="display: none"
     onclick="if(event.target === this){ this.style.display='none' }">
    <div class="black-block simple-centred-block modal-block ">
        <span class="close" onclick="document.getElementById('{{$modalClass}}').style.display='none'">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M-6.99382e-07 16L6.56802 7.52941L9.50835 7.52941L16 16L13.0597 16L8.05728 9.26909L2.94033 16L-6.99382e-07 16Z" fill="#404040"/>
                <path d="M16 2.94707e-06L9.43198 8.47059L6.49165 8.47059L1.90735e-06 -6.99382e-07L2.94034 -3.59166e-07L7.94272 6.73091L13.0597 1.70928e-06L16 2.94707e-06Z" fill="#404040"/>
            </svg>
        </span>
        <p class="modal-title big-white-title mb-20">{{$title}}</p>
        @if(!empty($subTitle))
            <p class="grey-s-light-text mb-20">{{$subTitle}}</p>
        @endif
        @if(!empty($link))
            <a href="{{$link}}" class="cart-address">{{$linkCaption}}</a>
        @endif
        @if(!empty($downTitle))
            <p class="grey-s-light-text mb-20">{{$downTitle}}</p>
        @endif
        <p><b class="error-text" style="color: #ff5252;"></b></p>
        <a class="orange-btn mini-btn confirm-order-btn" style="cursor: pointer;">{{$btnCaption}}</a>
    </div>
</div>
@push('scripts')
    <script>
        document.querySelector('#{{$modalClass}} .confirm-order-btn').onclick = e => {
            {{$btnAction}}(e)
        }
    </script>
@endpush
