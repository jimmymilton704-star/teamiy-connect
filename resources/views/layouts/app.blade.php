<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard · Teamiy Connect')</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @stack('styles')
</head>

<body data-page="@yield('page', 'dashboard')">
    <div class="app" id="app">

        @include('partials.sidebar')

        <div class="content">
            @include('partials.navbar')

            <main class="main" id="view">
                @yield('content')
            </main>
        </div>
    </div>

    <div id="modalRoot"></div>
    <div id="toastRoot"></div>

    {{-- Core JS first --}}
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Page scripts after app.js --}}
    @stack('scripts')

    {{-- Do not load dashboard.js when using Laravel Blade dashboard --}}
    {{-- <script src="{{ asset('js/dashboard.js') }}"></script> --}}

    <script>
        document.addEventListener('click', function(e) {
            const toggleBtn = e.target.closest('[data-action="toggle-nav"]');

            if (toggleBtn) {
                e.preventDefault();

                const app = document.getElementById('app');

                if (app) {
                    app.classList.toggle('nav-collapsed');
                    app.classList.toggle('nav-open');
                }
            }
        });
    </script>
</body>

</html>