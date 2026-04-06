<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Akses Akun - KBC Kotabaru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="fixed inset-0 -z-20 bg-slate-100"></div>
    <div class="pointer-events-none fixed inset-x-0 top-0 -z-10 h-[420px] bg-[radial-gradient(circle_at_top_left,_#fdba74_0,_transparent_48%),radial-gradient(circle_at_top_right,_#7dd3fc_0,_transparent_45%)]"></div>
    <div class="pointer-events-none fixed inset-0 -z-10 bg-grid-fade opacity-35"></div>

    <main class="app-container py-8 sm:py-12">
        <a href="{{ route('home') }}" class="mx-auto flex w-fit items-center gap-3 rounded-2xl border border-white/80 bg-white/75 px-4 py-2 shadow-sm backdrop-blur">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white">KBC</div>
            <div>
                <div class="text-[10px] font-semibold tracking-[0.22em] text-orange-600 sm:text-xs">KOTABARU BASKETBALL</div>
                <div class="text-sm font-black text-slate-900 sm:text-base">Competition Center</div>
            </div>
        </a>

        <div class="mx-auto mt-6 w-full max-w-2xl rounded-3xl border border-slate-200/80 bg-white/95 p-6 shadow-sm backdrop-blur sm:mt-8 sm:p-8">
            @include('shared.alerts')
            @yield('content')
        </div>
    </main>
</body>

</html>

