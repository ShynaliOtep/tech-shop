<div class="location-dropdown">
    <div class="dropdown drrrr" onclick="dropdownToggle()">
        <div class="dropbtn drrrr">
            <span class="drrrr">
                @if(session()->get('select_city') === 2)
                    {{__('translations.Astana')}}
                @else
                    {{__('translations.Almaty')}}
                @endif
            </span>
            <svg class="dropbtn-icon drrrr" id="dropbtnIcon" width="10" height="6" viewBox="0 0 10 6" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 0L5.89499 6L4.05728 6L0 -4.37114e-07L1.83771 -3.56785e-07L4.9642 4.76773L8.16229 -8.03288e-08L10 0Z" fill="#404040"/>
            </svg>
        </div>
        <div id="myDropdown" class="dropdown-content">
            <a  @if(session()->get('select_city') === 1) class="city-selected" @endif href="{{route('selectCity', 1)}}">{{__('translations.Almaty')}}</a>
            <a @if(session()->get('select_city') === 2) class="city-selected" @endif href="{{route('selectCity', 2)}}">{{__('translations.Astana')}}</a>
        </div>
    </div>
</div>

<script>
    function dropdownToggle() {
        document.getElementById("myDropdown").classList.toggle("show");
        document.getElementById("dropbtnIcon").classList.toggle("show-icon")
    }

    window.onclick = function(event) {
        console.log(event.target)
        if (!event.target.matches('.drrrr')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>

<style>
    .dropbtn {
        color: #404040;
        font-weight: 400;
        font-size: 16px;
        border: none;
        cursor: pointer;
        animation-name: move;
        animation-duration: 2s; /* ← 2 секунды */
        animation-timing-function: ease-in-out;
        display: flex;
        align-items: center;
    }

    .show-icon{
        transform: rotate(180deg);
    }

    .dropbtn-icon {
        margin-left: 15px;
    }

    .dropbtn:hover, .dropbtn:focus {

    }

    .location-dropdown {
        position: relative;
        width: 144px;
        height: 49px;
    }
    .dropdown {
        width: 144px;
        position: absolute;
        top: 0;
        left: 0;
        background-color: #191919;
        border-radius: 30px;
        padding: 15px 25px;
    }

    .dropdown-content {
        border-radius: 15px;
        display: none;
        width: 100%;
        overflow: auto;
        z-index: 1;
        padding-top: 15px;
    }

    .dropdown-content a {
        font-weight: 400;
        font-size: 16px;
        padding: 8px 0;
        text-decoration: none;
        display: block;
        color: #404040;
    }
    .city-selected {
        color: #ffffff !important;
    }

    .dropdown a:hover {background-color: #191919;}

    .show {display: block;}
    @media (max-width: 600px) {
        .dropbtn{
            font-size: 12px;
        }
        .dropdown-content {
            min-width: 100px;
        }
    }
</style>

