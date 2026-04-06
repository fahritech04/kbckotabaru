@php
    $menu = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard'],
        ['label' => 'Turnamen', 'route' => 'admin.tournaments.index', 'active' => 'admin.tournaments.*'],
        ['label' => 'Klub', 'route' => 'admin.clubs.index', 'active' => 'admin.clubs.*'],
        ['label' => 'Jadwal', 'route' => 'admin.schedules.index', 'active' => 'admin.schedules.*'],
        ['label' => 'Pertandingan', 'route' => 'admin.matches.index', 'active' => 'admin.matches.*'],
    ];
@endphp

<aside class="border-b border-slate-800 bg-slate-950 px-4 py-5 text-slate-200 md:border-b-0 md:border-r md:px-5 md:py-8 lg:px-6">
    <a href="{{ route('admin.dashboard') }}" class="mb-6 flex items-center gap-3 lg:mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 font-black text-white">KBC</div>
        <div>
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">Admin Panel</div>
            <div class="text-sm font-black">Kotabaru Basketball</div>
        </div>
    </a>

    <nav class="grid grid-cols-2 gap-2 text-sm font-semibold md:grid-cols-1">
        @foreach ($menu as $item)
            <a href="{{ route($item['route']) }}" class="rounded-xl px-3 py-2 {{ request()->routeIs($item['active']) ? 'bg-orange-500 text-white' : 'text-slate-300 hover:bg-slate-800' }}">{{ $item['label'] }}</a>
        @endforeach
    </nav>

    <div class="mt-6 grid grid-cols-2 gap-2 text-sm md:mt-10 md:grid-cols-1">
        <a href="{{ route('home') }}" class="rounded-xl border border-slate-700 px-3 py-2 text-center text-slate-200 transition hover:bg-slate-800">Lihat Website</a>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-xl bg-slate-800 px-3 py-2 font-semibold text-slate-200 transition hover:bg-slate-700">Logout</button>
        </form>
    </div>
</aside>
