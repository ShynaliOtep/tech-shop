
    <div class="dropdown-pp">
        <div onclick="dropdownToggleProfile()" class="dropbtn-profile">
            <div class="dropbtn-profile-btn">
                <img class="dropbtn-icon-profile" src="/img/profile-new.svg" alt="">
                <span>Профиль</span>
            </div>
            <div id="profileDropdown" class="dropdown-content-2">
                <a href="{{route('getMyOrders')}}" class="profile-dropdown-link white-text">{{__('translations.My orders')}}</a>
                <a href="{{route('viewProfile')}}" class="profile-dropdown-link white-text">{{__('translations.Check profile')}}</a>
                <br>
                <a href="{{route('logout')}}" class="white-text profile-dropdown-link">{{__('translations.Logout')}}</a>
            </div>
        </div>
    </div>

<script>
    function dropdownToggleProfile() {
        document.getElementById("profileDropdown").classList.toggle("show");
    }

    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn-profile')) {
            var dropdowns = document.getElementsByClassName("dropdown-content-2");
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
    .dropbtn-profile {
        position: absolute;
        width: 173px;
        background-color: #191919;
        border-radius: 30px;
        color: #404040;
        font-weight: 400;
        font-size: 16px;
        padding: 15px 15px;
        border: none;
        cursor: pointer;
        animation-name: move;
        animation-duration: 2s; /* ← 2 секунды */
        animation-timing-function: ease-in-out;
    }
    .dropbtn-profile-btn {
        display: flex;
        gap: 15px
    }
    .dropbtn-icon-profile {
        margin-left: 10px;
    }

    .dropdown-pp {
        position: relative;
        width: 173px;
    }

    .dropdown-content-2 {
        display: none;
        width: 100%;
        overflow: auto;
        z-index: 1;
    }

    .dropdown-content-2 a {
        font-weight: 400;
        font-size: 16px;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        color: #404040;
    }
    .dropdown-content-2 a:hover {
        color: #ffffff;
    }

    .dropdown a:hover {background-color: #191919;}

    .show {display: block;}
</style>
