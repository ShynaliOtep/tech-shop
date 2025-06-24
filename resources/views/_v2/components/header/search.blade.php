<div class="search-block search-wrapper valign-wrapper input-field">
    <input id="search" type="text"
           class="search-header-input  validate browser-default text-white center-align autocomplete"
           placeholder="Поиск товаров...">
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var elems = document.querySelectorAll('.autocomplete');
            var data = {
                @foreach($goodOptions as $goodOption)
                "{{$goodOption['name']}}": "{{$goodOption['url']}}",
                @endforeach
            };
            var instances = M.Autocomplete.init(elems, {
                data: data,
                limit: 5,
                onAutocomplete: (item) => {
                    window.location.href = '/autofill/' + item
                }
            });
        });
    </script>
@endpush

<style>
    .search-header-input {
        width: 350px;
        height: 50px;
        border-radius: 10000px;
        outline: none;
        background: url(../img/search-new.svg) 300px center / 20px no-repeat scroll,#151515;
        border-width: 2px;
        border-style: solid;
        border-color: #191919;
        border-image: initial;
        padding: 1% 20px;
        transition: 0.7ms;
        color: #404040;
        font-weight: 400;
        font-size: 16px;
        position: absolute;
        top: 0;
        z-index: 10;
    }
    .search-block {
        margin-left: 50px;
        position: relative;
        height: 50px;
    }
    @media (max-width: 600px) {
        .search-block {
            margin-left: 0;
            margin-top: 20px;
            width: 100%;
        }
        .search-header-input {
            background: url(../img/search-new.svg) 320px center / 20px no-repeat scroll,#151515;
            width: 100%;
            font-size: 12px;
        }
    }
</style>



