<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin KBC Kotabaru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="grid min-h-screen lg:grid-cols-[280px_1fr]">
        <aside class="bg-slate-950 px-4 py-6 text-slate-200 sm:px-6 lg:px-6 lg:py-8">
            <a href="{{ route('admin.dashboard') }}" class="mb-6 flex items-center gap-3 lg:mb-8">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 font-black text-white">KBC</div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">Admin Panel</div>
                    <div class="text-sm font-black">Kotabaru Basketball</div>
                </div>
            </a>

            <nav class="grid grid-cols-2 gap-2 text-sm font-semibold sm:grid-cols-3 lg:grid-cols-1">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('admin.dashboard') ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Dashboard</a>
                <a href="{{ route('admin.tournaments.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('admin.tournaments.*') ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Turnamen</a>
                <a href="{{ route('admin.clubs.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('admin.clubs.*') ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Klub</a>
                <a href="{{ route('admin.schedules.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('admin.schedules.*') ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Jadwal</a>
                <a href="{{ route('admin.matches.index') }}" class="block rounded-lg px-3 py-2 {{ request()->routeIs('admin.matches.*') ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">Pertandingan</a>
            </nav>

            <div class="mt-6 grid grid-cols-2 gap-2 text-sm lg:mt-10 lg:grid-cols-1">
                <a href="{{ route('home') }}" class="block rounded-lg border border-slate-700 px-3 py-2 text-center text-slate-200 hover:bg-slate-800">Lihat Website</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-slate-800 px-3 py-2 font-semibold text-slate-200 hover:bg-slate-700">Logout</button>
                </form>
            </div>
        </aside>

        <div>
            <header class="border-b border-slate-200 bg-white px-4 py-4 sm:px-6">
                <div class="text-sm text-slate-500">Selamat datang, <span class="font-semibold text-slate-700">{{ $adminAuthUser['name'] ?? 'Admin' }}</span></div>
            </header>

            <main class="px-4 py-6 sm:px-6">
                @include('partials.alerts')
                @yield('content')
            </main>
        </div>
    </div>
</body>

</html>

