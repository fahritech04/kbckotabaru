@php
    $menu = [
        ['label' => 'Beranda', 'route' => 'home', 'active' => 'home'],
        ['label' => 'Pertandingan', 'route' => 'matches.index', 'active' => 'matches.*'],
        ['label' => 'Jadwal', 'route' => 'schedules.index', 'active' => 'schedules.*'],
        ['label' => 'Klub', 'route' => 'clubs.index', 'active' => 'clubs.*'],
        ['label' => 'Turnamen', 'route' => 'tournaments.index', 'active' => 'tournaments.*'],
        ['label' => 'Instagram', 'route' => 'instagram.index', 'active' => 'instagram.*'],
    ];
@endphp

<header id="public-navbar" data-fixed-navbar class="fixed inset-x-0 top-0 z-40 border-b border-slate-200 bg-white">
    <div class="app-container py-3 sm:py-4">
        <div class="relative flex items-center gap-3">
            <a href="{{ route('home') }}" class="mr-auto flex items-center gap-3">
                <div>
                    <div class="text-[10px] font-semibold tracking-[0.22em] text-orange-600 sm:text-xs">KOTABARU BASKETBALL</div>
                    <div class="text-sm font-black text-slate-900 sm:text-base">Competition Center</div>
                </div>
            </a>

            <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-end lg:gap-4">
                <nav class="flex items-center gap-1">
                    @foreach ($menu as $item)
                        <a href="{{ route($item['route']) }}" class="nav-link {{ request()->routeIs($item['active']) ? 'nav-link-active' : '' }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>
            </div>

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
                </div>
            </details>
        </div>
    </div>
</header>
