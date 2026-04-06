@extends('layouts.public-site', ['title' => $tournament['name'].' - KBC Kotabaru'])

@section('content')
    <section class="rounded-3xl bg-slate-900 px-5 py-8 text-white sm:px-8 sm:py-10">
        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">{{ $tournament['season'] }}</div>
        <h1 class="mt-3 text-3xl font-black sm:text-4xl">{{ $tournament['name'] }}</h1>
        <p class="mt-3 max-w-3xl text-sm text-slate-300">{{ $tournament['description'] ?? 'Turnamen ini mempertemukan klub basket terbaik di Kotabaru.' }}</p>
        <div class="mt-4 flex flex-wrap gap-3 text-xs font-semibold">
            <span class="rounded-full bg-white/10 px-3 py-1">Lokasi: {{ $tournament['location'] }}</span>
            <span class="rounded-full bg-white/10 px-3 py-1">Status: {{ $tournament['status'] }}</span>
            <span class="rounded-full bg-white/10 px-3 py-1">Periode: {{ $tournament['start_date'] }} s/d {{ $tournament['end_date'] }}</span>
        </div>
    </section>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-black text-slate-900">Pertandingan Terkait</h2>
            <a href="{{ route('matches.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Semua pertandingan</a>
        </div>
        <div class="space-y-3">
            @forelse ($matches as $match)
                <a href="{{ route('matches.show', $match['id']) }}" class="block rounded-xl border border-slate-100 p-4 hover:border-orange-200 hover:bg-orange-50/40">
                    <div class="text-xs text-slate-500">{{ $match['round'] ?? '-' }} • {{ $match['tipoff_at'] ?? '-' }}</div>
                    <div class="mt-2 flex flex-col items-center gap-2 text-sm font-bold text-slate-900 sm:flex-row sm:justify-between">
                        <span>{{ $match['home_club']['name'] ?? '-' }}</span>
                        <span class="rounded bg-slate-900 px-2 py-1 text-xs text-white">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</span>
                        <span>{{ $match['away_club']['name'] ?? '-' }}</span>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">Belum ada pertandingan untuk turnamen ini.</div>
            @endforelse
        </div>
    </section>
@endsection


