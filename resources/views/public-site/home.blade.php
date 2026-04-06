@extends('layouts.public-site', ['title' => 'KBC Kotabaru - Liga Basket'])

@section('content')
    <section class="relative overflow-hidden rounded-3xl bg-slate-900 px-5 py-8 text-white sm:px-8 sm:py-12">
        <div class="absolute -right-10 -top-12 h-52 w-52 rounded-full bg-orange-500/30 blur-3xl"></div>
        <div class="absolute -bottom-16 left-10 h-52 w-52 rounded-full bg-sky-400/20 blur-3xl"></div>
        <div class="relative grid gap-8 lg:grid-cols-[1.2fr_1fr] lg:items-center">
            <div>
                <p class="inline-flex rounded-full border border-orange-300/30 bg-orange-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-orange-200">Kotabaru Basketball Competition</p>
                <h1 class="mt-4 text-3xl font-black leading-tight sm:text-5xl">Pusat Informasi Liga Basket Kotabaru</h1>
                <p class="mt-4 max-w-xl text-sm text-slate-300 sm:text-base">Ikuti update pertandingan, jadwal resmi, profil klub, dan progress turnamen dalam satu platform modern berbasis Firebase.</p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('matches.index') }}" class="rounded-xl bg-orange-500 px-5 py-3 text-sm font-bold text-white hover:bg-orange-600">Lihat Pertandingan</a>
                    <a href="{{ route('tournaments.index') }}" class="rounded-xl border border-white/20 px-5 py-3 text-sm font-bold text-white hover:bg-white/10">Lihat Turnamen</a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                    <div class="text-xs uppercase tracking-wider text-slate-300">Turnamen</div>
                    <div class="mt-2 text-3xl font-black">{{ $stats['tournaments'] ?? 0 }}</div>
                </div>
                <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                    <div class="text-xs uppercase tracking-wider text-slate-300">Klub</div>
                    <div class="mt-2 text-3xl font-black">{{ $stats['clubs'] ?? 0 }}</div>
                </div>
                <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                    <div class="text-xs uppercase tracking-wider text-slate-300">Jadwal</div>
                    <div class="mt-2 text-3xl font-black">{{ $stats['schedules'] ?? 0 }}</div>
                </div>
                <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                    <div class="text-xs uppercase tracking-wider text-slate-300">Laga Selesai</div>
                    <div class="mt-2 text-3xl font-black">{{ $stats['finished_matches'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-black text-slate-900">Pertandingan Mendatang</h2>
                <a href="{{ route('matches.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Lihat semua</a>
            </div>
            <div class="space-y-3">
                @forelse ($upcomingMatches as $match)
                    <a href="{{ route('matches.show', $match['id']) }}" class="block rounded-xl border border-slate-100 p-4 hover:border-orange-200 hover:bg-orange-50/40">
                        <div class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ $match['round'] ?? 'Regular Round' }} • {{ $match['tournament']['name'] ?? 'Turnamen' }}</div>
                        <div class="mt-2 flex flex-col items-center gap-2 text-sm font-bold sm:flex-row sm:justify-between">
                            <span>{{ $match['home_club']['name'] ?? 'Home Club' }}</span>
                            <span class="rounded bg-slate-100 px-2 py-1 text-xs text-slate-500">VS</span>
                            <span>{{ $match['away_club']['name'] ?? 'Away Club' }}</span>
                        </div>
                        <div class="mt-2 text-xs text-slate-500">{{ ! empty($match['tipoff_at']) ? \Illuminate\Support\Carbon::parse($match['tipoff_at'])->format('d M Y, H:i') : '-' }} • {{ $match['venue'] ?? '-' }}</div>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada data pertandingan mendatang.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-black text-slate-900">Hasil Terbaru</h2>
                <a href="{{ route('matches.index', ['status' => 'selesai']) }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Lihat hasil</a>
            </div>
            <div class="space-y-3">
                @forelse ($latestMatches as $match)
                    <a href="{{ route('matches.show', $match['id']) }}" class="block rounded-xl border border-slate-100 p-4 hover:border-sky-200 hover:bg-sky-50/40">
                        <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-sm font-bold text-slate-900">{{ $match['home_club']['name'] ?? 'Home Club' }} vs {{ $match['away_club']['name'] ?? 'Away Club' }}</div>
                                <div class="mt-1 text-xs text-slate-500">{{ $match['tournament']['name'] ?? 'Turnamen' }}</div>
                            </div>
                            <div class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm font-black text-white">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada hasil pertandingan.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-10 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-black text-slate-900">Turnamen Aktif</h2>
                <a href="{{ route('tournaments.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Semua turnamen</a>
            </div>
            <div class="space-y-3">
                @forelse (collect($tournaments)->take(4) as $tournament)
                    <a href="{{ route('tournaments.show', $tournament['id']) }}" class="block rounded-xl border border-slate-100 p-4 hover:border-orange-200 hover:bg-orange-50/40">
                        <div class="text-sm font-black text-slate-900">{{ $tournament['name'] }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $tournament['season'] }} • {{ $tournament['location'] }}</div>
                        <div class="mt-2 inline-flex rounded-full bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase tracking-wide text-slate-600">{{ $tournament['status'] }}</div>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada turnamen.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-lg font-black text-slate-900">Daftar Klub</h2>
                <a href="{{ route('clubs.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Semua klub</a>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
                @forelse (collect($clubs)->take(6) as $club)
                    <a href="{{ route('clubs.show', $club['id']) }}" class="rounded-xl border border-slate-100 p-4 hover:border-sky-200 hover:bg-sky-50/30">
                        <div class="text-sm font-black text-slate-900">{{ $club['name'] }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $club['city'] }}</div>
                        <div class="mt-2 text-xs text-slate-400">W-L: {{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</div>
                    </a>
                @empty
                    <div class="col-span-2 rounded-xl border border-dashed border-slate-200 p-6 text-center text-sm text-slate-500">Belum ada klub.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection


