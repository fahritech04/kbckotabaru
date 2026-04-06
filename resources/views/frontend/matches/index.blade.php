@extends('layouts.app', ['title' => 'Pertandingan - KBC Kotabaru'])

@section('content')
    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 sm:text-3xl">Pertandingan</h1>
                <p class="mt-2 text-sm text-slate-500">Semua hasil dan jadwal laga KBC Kotabaru.</p>
            </div>
            <form method="GET" class="flex w-full flex-col items-stretch gap-2 sm:w-auto sm:flex-row sm:items-center">
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm sm:w-auto">
                    <option value="">Semua status</option>
                    @foreach (['scheduled', 'live', 'selesai', 'postponed'] as $status)
                        <option value="{{ $status }}" @selected($statusFilter === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white">Filter</button>
            </form>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-2">
        @forelse ($matches as $match)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <span>{{ $match['tournament']['name'] ?? 'Turnamen' }}</span>
                    <span>{{ $match['status'] ?? '-' }}</span>
                </div>
                <div class="mt-3 flex flex-col items-center gap-2 sm:flex-row sm:justify-between sm:gap-3">
                    <div class="text-sm font-bold text-slate-900">{{ $match['home_club']['name'] ?? '-' }}</div>
                    <div class="rounded-lg bg-slate-900 px-3 py-1 text-sm font-black text-white">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</div>
                    <div class="text-sm font-bold text-slate-900">{{ $match['away_club']['name'] ?? '-' }}</div>
                </div>
                <div class="mt-3 text-xs text-slate-500">{{ $match['round'] ?? '-' }} • {{ $match['tipoff_at'] ?? '-' }} • {{ $match['venue'] ?? '-' }}</div>
                <a href="{{ route('matches.show', $match['id']) }}" class="mt-4 inline-flex text-sm font-semibold text-orange-600 hover:text-orange-700">Lihat detail</a>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data pertandingan.</div>
        @endforelse
    </section>
@endsection

