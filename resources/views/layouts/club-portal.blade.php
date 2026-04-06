<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Klub KBC' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-[320px] bg-[radial-gradient(circle_at_top_left,_#fdba74_0,_transparent_45%),radial-gradient(circle_at_top_right,_#7dd3fc_0,_transparent_38%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-grid-fade opacity-35"></div>

    @include('shared.navigation.club-portal')
    <div data-navbar-spacer aria-hidden="true"></div>

    <main class="app-container py-6">
        @include('shared.alerts')
        @yield('content')
    </main>
</body>

</html>

