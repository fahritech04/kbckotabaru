@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Turnamen', 'route' => 'admin.tournaments.index', 'active' => 'admin.tournaments.*'],
        ['label' => 'Klub', 'route' => 'admin.clubs.index', 'active' => 'admin.clubs.*'],
        ['label' => 'Jadwal', 'route' => 'admin.schedules.index', 'active' => 'admin.schedules.*'],
        ['label' => 'Pertandingan', 'route' => 'admin.matches.index', 'active' => 'admin.matches.*'],
    ];
@endphp

<aside class="fixed inset-x-0 top-0 z-50 border-b border-slate-800 bg-slate-950 text-slate-200 md:inset-y-0 md:left-0 md:w-[290px] md:overflow-y-auto md:border-b-0 md:border-r">
    <div class="px-4 py-4 md:px-5 md:py-8 lg:px-6">
        <div class="relative flex items-center justify-between md:block">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 font-black text-white">KBC</div>
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">Admin Panel</div>
                    <div class="text-sm font-black">Kotabaru Basketball</div>
                </div>
            </a>

            <details class="group md:hidden">
                <summary class="summary-reset inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-xl border border-slate-700 text-slate-100 transition hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round" />
                    </svg>
                </summary>

                <div class="absolute left-0 right-0 top-[calc(100%+0.85rem)] rounded-2xl border border-slate-800 bg-slate-950 p-3 shadow-2xl">
                    <nav class="grid gap-2 text-sm font-semibold">
                        @foreach ($menu as $item)
                            <a href="{{ route($item['route']) }}" class="rounded-xl px-3 py-2 {{ request()->routeIs($item['active']) ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <a href="{{ route('home') }}" class="rounded-xl border border-slate-700 px-3 py-2 text-center text-slate-200 transition hover:bg-slate-800">Lihat Website</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-slate-800 px-3 py-2 font-semibold text-slate-200 transition hover:bg-slate-700">Logout</button>
                        </form>
                    </div>
                </div>
            </details>
        </div>

        <div class="hidden md:block">
            <nav class="mt-8 grid gap-2 text-sm font-semibold">
                @foreach ($menu as $item)
                    <a href="{{ route($item['route']) }}" class="rounded-xl px-3 py-2 {{ request()->routeIs($item['active']) ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            <div class="mt-10 grid gap-2 text-sm">
                <a href="{{ route('home') }}" class="rounded-xl border border-slate-700 px-3 py-2 text-center text-slate-200 transition hover:bg-slate-800">Lihat Website</a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-slate-800 px-3 py-2 font-semibold text-slate-200 transition hover:bg-slate-700">Logout</button>
                </form>
            </div>
        </div>
    </div>
</aside>
