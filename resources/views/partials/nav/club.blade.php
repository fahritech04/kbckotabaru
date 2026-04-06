@php
    $menu = [
        ['label' => 'Onboarding', 'route' => 'club.onboarding', 'active' => 'club.onboarding*'],
        ['label' => 'Profil Klub', 'route' => 'club.dashboard', 'active' => 'club.dashboard'],
        ['label' => 'Pemain', 'route' => 'club.players.index', 'active' => 'club.players.*'],
    ];
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="app-container py-4">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('club.dashboard') }}" class="mr-auto flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-black text-white">KBC</div>
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

            <details class="w-full lg:hidden">
                <summary class="summary-reset btn-secondary inline-flex cursor-pointer items-center gap-2">
                    Menu Klub
                    <span class="text-xs text-slate-500">Tap untuk buka</span>
                </summary>
                <div class="mt-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
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
