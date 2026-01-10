<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, Matrix lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, Matrix admin lite design, Matrix admin lite dashboard bootstrap 5 dashboard template"/>
    <meta name="description" content="Matrix Admin Lite Free Version is powerful and clean admin dashboard template, inpired from Bootstrap Framework"/>
    <meta name="robots" content="noindex,nofollow"/>

    <title>{{ $title }}</title>

    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}"/>
    <link rel="stylesheet" href="{{ asset('css/backend/dist/style.min.css') }}"/>
</head>

<body class="bg-dark">
<div class="main-wrapper">
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
        <div class="auth-box bg-dark border-top border-secondary">

            {{ $slot }}

        </div>
    </div>
</div>

<script src="{{ asset('js/backend/libs/jquery-3.3.1.min.js') }}"></script>

<script>
    $(".preloader").fadeOut();
    $("#to-recover").on("click", function () {
        $("#loginform").slideUp();
        $("#recoverform").fadeIn();
    });
    $("#to-login").click(function () {
        $("#recoverform").hide();
        $("#loginform").fadeIn();
    });
</script>
</body>
</html>
