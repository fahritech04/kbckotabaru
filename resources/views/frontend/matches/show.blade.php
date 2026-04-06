@extends('layouts.app', ['title' => 'Detail Pertandingan - KBC Kotabaru'])

@section('content')
    <section class="rounded-3xl bg-slate-900 px-5 py-8 text-white sm:px-8 sm:py-10">
        <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">{{ $match['tournament']['name'] ?? 'Turnamen' }}</div>
        <div class="mt-6 grid gap-4 sm:grid-cols-[1fr_auto_1fr] sm:items-center">
            <div class="text-center sm:text-right">
                <div class="text-xl font-black sm:text-3xl">{{ $match['home_club']['name'] ?? '-' }}</div>
                <div class="mt-1 text-xs text-slate-300">Home</div>
            </div>
            <div class="rounded-2xl bg-white/10 px-5 py-4 text-center sm:px-6">
                <div class="text-3xl font-black sm:text-4xl">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</div>
                <div class="mt-1 text-xs uppercase tracking-wide text-slate-300">{{ $match['status'] ?? '-' }}</div>
            </div>
            <div class="text-center sm:text-left">
                <div class="text-xl font-black sm:text-3xl">{{ $match['away_club']['name'] ?? '-' }}</div>
                <div class="mt-1 text-xs text-slate-300">Away</div>
            </div>
        </div>
        <div class="mt-6 grid gap-3 text-xs sm:grid-cols-3">
            <div class="rounded-xl bg-white/10 px-3 py-2">Round: {{ $match['round'] ?? '-' }}</div>
            <div class="rounded-xl bg-white/10 px-3 py-2">Tipoff: {{ $match['tipoff_at'] ?? '-' }}</div>
            <div class="rounded-xl bg-white/10 px-3 py-2">Venue: {{ $match['venue'] ?? '-' }}</div>
        </div>
    </section>

    @if (! empty($match['highlight']))
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-900">Highlight</h2>
            <p class="mt-3 text-sm text-slate-700">{{ $match['highlight'] }}</p>
        </section>
    @endif
@endsection

