@php
    $menu = [
        ['label' => 'Onboarding', 'route' => 'club.onboarding', 'active' => 'club.onboarding*'],
        ['label' => 'Profil Klub', 'route' => 'club.dashboard', 'active' => 'club.dashboard'],
        ['label' => 'Pemain', 'route' => 'club.players.index', 'active' => 'club.players.*'],
    ];
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="app-container py-4">
        <div class="relative flex items-center gap-3">
            <a href="{{ route('home') }}" class="mr-auto flex items-center gap-3">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-600">Portal Klub</div>
                    <div class="text-sm font-black text-slate-900">Kotabaru Basketball</div>
                </div>
            </a>

            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($menu as $item)
                    <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['active']) ? 'nav-link-active' : '' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <form method="POST" action="{{ route('club.logout') }}" class="hidden lg:block">
                @csrf
                <button type="submit" class="btn-secondary">Logout Klub</button>
            </form>

            <details class="static ml-auto lg:hidden">
                <summary class="summary-reset flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-slate-300 text-slate-700 transition hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round" />
                    </svg>
                </summary>
                <div class="absolute left-0 right-0 top-[calc(100%+0.75rem)] rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
                    <nav class="grid gap-1">
                        @foreach ($menu as $item)
                            <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['active']) ? 'nav-link-active' : '' }}">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>
                    <form method="POST" action="{{ route('club.logout') }}" class="mt-3 border-t border-slate-100 pt-3">
                        @csrf
                        <button type="submit" class="btn-secondary w-full">Logout Klub</button>
                    </form>
                </div>
            </details>
        </div>
    </div>
</header>
