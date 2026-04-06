<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin KBC Kotabaru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="pointer-events-none fixed inset-0 -z-20 bg-slate-100"></div>
    <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-[320px] bg-[radial-gradient(circle_at_top_left,_#fdba74_0,_transparent_48%),radial-gradient(circle_at_top_right,_#7dd3fc_0,_transparent_40%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-grid-fade opacity-30"></div>

    <div class="grid min-h-screen md:grid-cols-[290px_1fr]">
        @include('partials.nav.admin')

        <div>
            <header class="border-b border-slate-200/80 bg-white/80 px-4 py-4 backdrop-blur sm:px-6">
                <div class="text-sm text-slate-500">Selamat datang, <span class="font-semibold text-slate-700">{{ $adminAuthUser['name'] ?? 'Admin' }}</span></div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8">
                @include('partials.alerts')
                <div class="mx-auto w-full max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>

</html>

