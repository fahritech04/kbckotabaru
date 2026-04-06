<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'KBC Kotabaru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-800">
    @php
        $authUser = session(\App\Services\SessionAuthService::SESSION_KEY_CLUB);
        $isClubAuthenticated = ($authUser['role'] ?? null) === 'club';
    @endphp

    <div class="absolute inset-x-0 top-0 -z-10 h-[420px] bg-[radial-gradient(circle_at_top_right,_#f97316_0,_transparent_55%),radial-gradient(circle_at_top_left,_#0ea5e9_0,_transparent_40%)]"></div>

    <header class="border-b border-white/60 bg-white/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl flex-col items-start gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white">KBC</div>
                <div>
                    <div class="text-xs font-semibold tracking-[0.2em] text-orange-600">KOTABARU BASKETBALL</div>
                    <div class="text-sm font-black text-slate-900">Competition Center</div>
                </div>
            </a>

            <div class="w-full overflow-x-auto pb-1 lg:flex-1 lg:pb-0">
                <nav class="flex min-w-max items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('home') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Beranda</a>
                    <a href="{{ route('matches.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('matches.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Pertandingan</a>
                    <a href="{{ route('schedules.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('schedules.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Jadwal</a>
                    <a href="{{ route('clubs.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('clubs.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Klub</a>
                    <a href="{{ route('tournaments.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('tournaments.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Turnamen</a>
                </nav>
            </div>

            <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
                @if ($isClubAuthenticated)
                    <div class="w-full text-xs font-semibold text-slate-500 sm:w-auto">Klub: {{ $authUser['name'] ?? 'Tim Klub' }}</div>
                    <a href="{{ route('club.dashboard') }}" class="inline-flex flex-1 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 sm:flex-none">Dashboard Klub</a>
                    <form method="POST" action="{{ route('club.logout') }}" class="flex-1 sm:flex-none">
                        @csrf
                        <button type="submit" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Logout Klub</button>
                    </form>
                @else
                    <a href="{{ route('club.login') }}" class="inline-flex flex-1 items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 sm:flex-none">Login Klub</a>
                    <a href="{{ route('club.register') }}" class="inline-flex flex-1 items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 sm:flex-none">Daftar Klub</a>
                @endif
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        @include('partials.alerts')
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm text-slate-500 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>© {{ date('Y') }} KBC Kotabaru. Semua hak cipta dilindungi.</div>
            <div>Platform kompetisi basket daerah dengan data real-time Firebase.</div>
        </div>
    </footer>
</body>

</html>

