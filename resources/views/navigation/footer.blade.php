<footer class="page-footer grey darken-4 container">
    <div class="row center-align">
        <a class="tertimony" href="https://www.instagram.com/pixel_rental.kz/?hl=en">
            <img src="{{asset('/img/instagram.jpg')}}" height="150px" alt="">
        </a>
        <a class="tertimony" href="http://wa.link/pms0of">
            <img src="{{asset('/img/whatsapp.jpg')}}" height="150px" alt="">
        </a>
        <a class="tertimony" href="https://web.telegram.org/k/#@pixelrental">
            <img src="{{asset('/img/telegram.jpg')}}" height="150px" alt="">
        </a>
        <a class="tertimony" href="https://2gis.kz/almaty/geo/70000001069136996">
            <img src="{{asset('/img/twogis.png')}}" height="150px" alt="">
        </a>
        <div class="footer-copyright">
            <div class="container">
            <span class="center">
            {{ now()->tz('Asia/Almaty')->year }}<a href="https://msbtrust.kz"><b class="orange-text text-lighten-1"> ©MSB trust</b></a>
            </span>
            </div>
        </div>
    </div>

</footer>
<style>
    .tertimony {
        margin-left: 20px;
        margin-top: 10px;
    }
</style>
