@extends('layouts.admin', ['title' => 'Dashboard Admin - KBC Kotabaru'])

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Turnamen</div>
            <div class="mt-2 text-3xl font-black text-slate-900">{{ $stats['tournaments'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Klub</div>
            <div class="mt-2 text-3xl font-black text-slate-900">{{ $stats['clubs'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Jadwal</div>
            <div class="mt-2 text-3xl font-black text-slate-900">{{ $stats['schedules'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Pertandingan</div>
            <div class="mt-2 text-3xl font-black text-slate-900">{{ $stats['matches'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-xs uppercase tracking-wide text-slate-500">Selesai</div>
            <div class="mt-2 text-3xl font-black text-slate-900">{{ $stats['finished_matches'] ?? 0 }}</div>
        </div>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-900">Pertandingan Terbaru</h2>
            <div class="mt-4 space-y-3">
                @forelse ($latestMatches as $match)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <div class="text-sm font-bold text-slate-900">{{ $match['home_club']['name'] ?? '-' }} vs {{ $match['away_club']['name'] ?? '-' }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $match['status'] ?? '-' }} • {{ $match['tipoff_at'] ?? '-' }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data pertandingan.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-slate-900">Jadwal Terbaru</h2>
            <div class="mt-4 space-y-3">
                @forelse ($latestSchedules as $schedule)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <div class="text-sm font-bold text-slate-900">{{ $schedule['title'] }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $schedule['scheduled_at'] ?? '-' }} • {{ $schedule['venue'] ?? '-' }}</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada data jadwal.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection

