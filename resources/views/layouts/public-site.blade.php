<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KBC Kotabaru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">
    <div class="fixed inset-0 -z-20 bg-slate-50"></div>
    <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-[450px] bg-[radial-gradient(circle_at_top_right,_#fdba74_0,_transparent_52%),radial-gradient(circle_at_top_left,_#7dd3fc_0,_transparent_42%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-grid-fade opacity-40"></div>

    @include('shared.navigation.public-site')
    <div data-navbar-spacer aria-hidden="true"></div>

    <main class="app-container pb-8 pt-6 sm:pb-10 sm:pt-8">
        @include('shared.alerts')
        @yield('content')
    </main>

    @include('shared.footer.public-site')
</body>

</html>


