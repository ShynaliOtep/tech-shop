<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>pixelrental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="{{  asset('css/app.css') }}" rel="stylesheet">
</head>
<style>
    html {
        padding: 0;
        margin: 0;
        overflow-x: hidden;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }

    body {
        padding: 0;
        margin: 0;
        background-color: black !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }

    h5 {
        margin: 16.4px 0px;
    }

    .input-field label {
        color: white;
    }

    .input-field input[type=text]:focus + label {
        color: white;
    }

    .input-field input[type=text]:focus {
        border-bottom: 1px solid white;
        box-shadow: 0 1px 0 0 white;
    }

    .input-field input[type=text].valid {
        border-bottom: 1px solid white;
        box-shadow: 0 1px 0 0 white;
    }

    .input-field input[type=text].invalid {
        border-bottom: 1px solid white;
        box-shadow: 0 1px 0 0 white;
    }

    .input-field .prefix.active {
        color: white;
    }

    .input-field input:focus + label {
        color: white !important;
    }

    .row .input-field input:focus {
        border-bottom: 1px solid white !important;
        box-shadow: 0 1px 0 0 white !important
    }

    ::-webkit-scrollbar {
        display: none;
    }

    td[aria-selected="false"]:not(.is-disabled) {
        color: black;
    }

    li.disabled > span {
        color: white !important;
    }

    .timepicker-canvas line {
        stroke: white;
    }

    hr {
        margin: 0;
    }

    .section-search input.autocomplete {
        color: #000;
    }

    .input-field .prefix.active {
        color: #000 !important;
    }

    .autocomplete-content li > a, .autocomplete-content li > span {
        color: #000 !important;
    }

    .autocomplete-content li .highlight {
        color: white !important;
    }

    .dropdown-content li > span {
        color: black !important;
    }

    .main-color {
        background-color: #161616;
    }

    .nav-wrapper {
        width: 100%;
    }

    .auth-container {
        height: 85vh;
        display: flex;
        justify-content: center;
    }

    .auth-inner {
        max-width: 500px;
    }

    .message-wrapper {
        border-radius: 15px;
        box-shadow: 0 0 20px 1px white;
        padding: 5%;
    }

</style>
<body>
<nav class="main-color auth-header">
    <div class="nav-wrapper">
        <div class="logo-wrapper center">
            <a href="https://pixelrental.kz" class="orange-text"><u>PixelRental.kz</u></a>
        </div>
    </div>
</nav>
<div class="container valign-wrapper auth-container center">
    <div class="main-color center auth-inner message-wrapper">
        <h5 class="white-text">{{__('translations.You are welcomed by the device rental')}} <a href="https://pixelrental.kz"
                                                                                 class="orange-text"><u>Pixelrental.kz</u></a>
        </h5>
        <h5 class="white-text">{{__('translations.Thank you for your loyalty!')}}</h5>
        <p class="white-text">{{__('translations.Confirm your email address by clicking on the link below')}}</p>
        <a href="{{route('confirmEmail', $clientEmail . 'pixelrental' . $confirmationCode)}}"
           class="btn btn-large orange darken-4 white-text">{{__('translations.Confirm email')}}</a>
    </div>
</div>
</body>
</html>
