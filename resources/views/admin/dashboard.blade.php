@extends('layouts.admin', ['title' => 'Dashboard Admin - KBC Kotabaru'])

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Dashboard Admin',
        'description' => 'Ringkasan real-time untuk seluruh data kompetisi KBC Kotabaru.',
    ])

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <article class="metric-card">
            <div class="metric-label">Turnamen</div>
            <div class="metric-value">{{ $stats['tournaments'] ?? 0 }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Klub</div>
            <div class="metric-value">{{ $stats['clubs'] ?? 0 }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Jadwal</div>
            <div class="metric-value">{{ $stats['schedules'] ?? 0 }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Pertandingan</div>
            <div class="metric-value">{{ $stats['matches'] ?? 0 }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Selesai</div>
            <div class="metric-value">{{ $stats['finished_matches'] ?? 0 }}</div>
        </article>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="surface-card p-5">
            <h2 class="text-lg font-black text-slate-900">Pertandingan Terbaru</h2>
            <div class="mt-4 space-y-3">
                @forelse ($latestMatches as $match)
                    <article class="rounded-xl border border-slate-100 p-3">
                        <div class="text-sm font-bold text-slate-900">{{ $match['home_club']['name'] ?? '-' }} vs {{ $match['away_club']['name'] ?? '-' }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $match['status'] ?? '-' }} • {{ $match['tipoff_at'] ?? '-' }}</div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data pertandingan.</p>
                @endforelse
            </div>
        </section>

        <section class="surface-card p-5">
            <h2 class="text-lg font-black text-slate-900">Jadwal Terbaru</h2>
            <div class="mt-4 space-y-3">
                @forelse ($latestSchedules as $schedule)
                    <article class="rounded-xl border border-slate-100 p-3">
                        <div class="text-sm font-bold text-slate-900">{{ $schedule['title'] }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $schedule['scheduled_at'] ?? '-' }} • {{ $schedule['venue'] ?? '-' }}</div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data jadwal.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
