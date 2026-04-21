<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'DompetKu')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
</head>

<body class="@yield('body-class', 'd-flex align-items-center justify-content-center vh-100') position-relative">
    {{-- Global Background Icons --}}
    <div class="position-fixed w-100 h-100 top-0 start-0 pointer-events-none opacity-25" style="z-index: -1;">
        <i class="bi bi-wallet2 position-absolute text-success" style="font-size: 3rem; top: 10%; left: 5%; transform: rotate(-15deg);"></i>
        <i class="bi bi-piggy-bank position-absolute text-success" style="font-size: 4rem; top: 70%; left: 8%; transform: rotate(15deg);"></i>
        <i class="bi bi-coin position-absolute text-warning" style="font-size: 2.5rem; top: 25%; left: 45%; transform: rotate(-10deg);"></i>
        <i class="bi bi-cash-stack position-absolute text-success" style="font-size: 3.5rem; bottom: 15%; left: 35%; transform: rotate(10deg);"></i>
        <i class="bi bi-bank position-absolute text-success" style="font-size: 4rem; top: 15%; right: 10%; transform: rotate(10deg);"></i>
        <i class="bi bi-safe position-absolute text-secondary" style="font-size: 3.5rem; bottom: 20%; right: 8%; transform: rotate(-15deg);"></i>
        <i class="bi bi-credit-card position-absolute text-primary" style="font-size: 3rem; top: 60%; right: 40%; transform: rotate(-5deg);"></i>
        <i class="bi bi-currency-dollar position-absolute text-success opacity-50" style="font-size: 2rem; top: 40%; left: 20%; transform: rotate(20deg);"></i>
        <i class="bi bi-graph-up-arrow position-absolute text-success opacity-50" style="font-size: 2rem; top: 80%; right: 25%; transform: rotate(-15deg);"></i>
    </div>

    <div class="flex-grow-1">
        @yield('content')
    </div>

    @auth
        @include('includes.footer')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')

    <script>
        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js").then(function (reg) {
                console.log("Service worker has been registered for scope: " + reg.scope);
            });
        }
    </script>
</body>

</html>
