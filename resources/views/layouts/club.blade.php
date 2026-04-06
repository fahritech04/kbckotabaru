<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal Klub KBC' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <a href="{{ route('club.dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white">KBC</div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-600">Portal Klub</div>
                    <div class="text-sm font-black text-slate-900">Kotabaru Basketball</div>
                </div>
            </a>

            <div class="w-full overflow-x-auto lg:w-auto">
                <nav class="flex min-w-max items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('club.onboarding') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('club.onboarding*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Onboarding</a>
                    <a href="{{ route('club.dashboard') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('club.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Profil Klub</a>
                    <a href="{{ route('club.players.index') }}" class="rounded-lg px-3 py-2 {{ request()->routeIs('club.players.*') ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Pemain</a>
                </nav>
            </div>

            <form method="POST" action="{{ route('club.logout') }}" class="w-full lg:w-auto">
                @csrf
                <button type="submit" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 lg:w-auto">Logout Klub</button>
            </form>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('partials.alerts')
        @yield('content')
    </main>
</body>

</html>
