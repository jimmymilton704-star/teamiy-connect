<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · Teamiy Connect</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href=" {{ asset('css/login.css') }}">
</head>

<body data-page="login">
    <div class="login" id="login">
        <div class="login-panel">
            <div class="blob1"></div>
            <div class="blob2"></div>
            <div class="login-brand">
                <div class="login-mark">T</div>
                <span style="font-weight:800;font-size:19px;letter-spacing:-.01em">Teamiy Connect</span>
            </div>
            <div class="login-hero">
                <h1>Your whole workday, in one calm place.</h1>
                <p>Leave, attendance, projects, assets, meetings and TADA — everything an employee needs, without the
                    spreadsheet chaos.</p>
                <div class="login-stats">
                    <div>
                        <div class="n">10</div>
                        <div class="l">Modules</div>
                    </div>
                    <div>
                        <div class="n">1 min</div>
                        <div class="l">To check in</div>
                    </div>
                    <div>
                        <div class="n">24/7</div>
                        <div class="l">Self-service</div>
                    </div>
                </div>
            </div>
            <div class="login-foot">© 2026 Teamiy Connect · v1.0</div>
        </div>
        @yield('content')
        <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>
