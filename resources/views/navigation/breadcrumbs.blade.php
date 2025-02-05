<div class="breadcrumbs-section">
    <div class="col s12">
        <a href="/" class="breadcrumb">
            <span class="breadcrumb-item chip orange darken-4 white-text">
                <b>
                {{__('translations.Main')}}
                </b>
            </span>
        </a>
        @if (Route::is('goodList') && isset($goodType))
            <a href="category/{{$goodType->code}}" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{__( 'translations.'. $goodType->code)}}
                    </b>
                </span>
            </a>
        @endif
        @if (Route::is('viewProfile'))
            <a href="#" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{__( 'translations.Profile')}}
                    </b>
                </span>
            </a>
        @endif
        @if (Route::is('editProfile'))
            <a href="{{route('viewProfile')}}" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{__( 'translations.Profile')}}
                    </b>
                </span>
            </a>
            <a href="#" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{__( 'translations.Editing')}}
                    </b>
                </span>
            </a>
        @endif
        @if (Route::is('viewGood') && isset($good))
            <a href="category/{{$good->goodType->code}}" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{__( 'translations.'. $good->goodType->code)}}
                    </b>
                </span>
            </a>
            <a href="{{route('viewGood', $good)}}" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{$good['name_' . session()->get('locale', 'ru')]}}
                    </b>
                </span>
            </a>
        @endif
        @if (Route::is('getFavorites'))
            <a href="{{route('getFavorites')}}" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                    <b>
                        {{__( 'translations.Favorites')}}
                    </b>
                </span>
            </a>
        @endif
        @if (Route::is('cart'))
            <a href="{{route('cart')}}" class="breadcrumb">
                <span class="breadcrumb-item chip orange darken-4 white-text">
                <b>
                    {{__( 'translations.Cart')}}
                </b>
                </span>
            </a>
        @endif
    </div>
</div>
