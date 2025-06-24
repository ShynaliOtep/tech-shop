<a href="/bonus" style="text-decoration: none; display: block">
    <div class="banner">
        <div class="banner-text-block">
            <h4 class="banner-text-1">
                КЭШБЭК
            </h4>
            <h4 class="banner-text-2">
                ДО <span>30%</span>
            </h4>
            <a href="/bonus" class="banner-button hide-m">Получить выгоду</a>
        </div>
        <div>
            <img src="/img/banner/asset_1.png" alt="" class="asset-1">
            <img src="/img/banner/asset_2.png" alt="" class="asset-2">
            <img src="/img/banner/asset_3.png" alt="" class="asset-3">
            <img src="/img/banner/asset_4.png" alt="" class="asset-4">
        </div>
    </div>
</a>

<style>
    .banner {
        width: 100%;
        height: 450px;
        display: block;
        background-color: #191919;
        border-radius: 30px;
        text-decoration: none !important;
        position: relative;
    }
    .banner-text-block {
        margin-left: 220px;
        padding-top: 50px;
        position: relative;
        z-index: 5;
    }
    .banner-text-1 {
        font-size: 131px;
        font-weight: 400;
        color: #ffffff;
        text-decoration: none !important;
        margin-bottom: 0;
        line-height: 100%;

    }
    .banner-text-2 {
        font-size: 140px;
        font-weight: 700;
        color: #ffffff;
        margin-top: 0;
        text-decoration: none !important;
        line-height: 100%;
    }
    .banner-text-2 span {
        color: #FF962E;
    }
    .banner-button {
        width: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 50px;
        text-decoration: none;
        border: 1px solid #FF962E;
        color: #FF962E;
        font-size: 16px;
        font-weight: 400;
        margin-top: 30px;
        border-radius: 1000px;
        z-index: 5;
        position: relative;
    }
    .asset-1 {
        position: absolute;
        top: -150px;
        left: 530px;
        clip-path: inset(150px 0 0 0);
    }
    .asset-2 {
        position: absolute;
        top: -60px;
        left: -50px;
        clip-path: inset(60px 0 140px 30px);
        z-index: 1;
    }
    .asset-3 {
        position: absolute;
        left: 450px;
        bottom: 0;
        z-index: 1;
    }
    .asset-4 {
        position: absolute;
        right: -60px;
        top: 10px;
        clip-path: inset(0 20px 0 0);
        z-index: 1;
        max-width: 50%;
    }
    @media (max-width: 1700px) {
        .asset-4 {
            top: 60px;
            max-width: 50%;
        }
        .banner-text-block {
            padding-top: 100px;
        }
        .banner-text-1 {
            font-size: 80px;
        }
        .banner-text-2 {
            font-size: 90px;
        }
    }
    @media (max-width: 600px) {
        .banner {
            height: 140px;
        }
        .banner-text-block {
            margin-left: 60px;
            padding-top: 40px;
        }
        .banner-text-1 {
            font-size: 31px;
        }
        .banner-text-2 {
            font-size: 34px;
        }
        .asset-2 {
            width: 116px;
            left: -20px;
            top: 0;
            clip-path: inset(0 0 0 10px);
        }
        .asset-1 {
            width: 92px;
            left: 40%;
            top: -50px;
            clip-path: inset(35px 0 0 0);
        }
        .asset-3 {
            width: 91px;
            clip-path: inset(30px 0 0 0);
            bottom: 0;
            left: 35%;
        }
        .asset-4 {
            width: 202px;
            clip-path: inset(0 10px 0 0);
            right: -20px;
            top: 0;
        }
    }
</style>
