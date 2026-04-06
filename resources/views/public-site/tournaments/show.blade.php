@extends('layouts.public-site', ['title' => $tournament['name'].' - KBC Kotabaru'])

@section('content')
    <section class="rounded-3xl bg-slate-900 px-5 py-8 text-white sm:px-8 sm:py-10">
        <div class="grid gap-6 lg:grid-cols-[1.35fr,1fr] lg:items-start">
            <div>
                <div class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-300">{{ $tournament['season'] }}</div>
                <h1 class="mt-3 text-3xl font-black sm:text-4xl">{{ $tournament['name'] }}</h1>
                <p class="mt-3 max-w-3xl text-sm text-slate-300">{{ $tournament['description'] ?? 'Turnamen ini mempertemukan klub basket terbaik di Kotabaru.' }}</p>
                <div class="mt-4 flex flex-wrap gap-3 text-xs font-semibold">
                    <span class="rounded-full bg-white/10 px-3 py-1">Lokasi: {{ $tournament['location'] }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1">Status: {{ $tournament['status'] }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1">Periode: {{ $tournament['start_date'] }} s/d {{ $tournament['end_date'] }}</span>
                    <span class="rounded-full bg-orange-500/20 px-3 py-1 text-orange-100">Sistem: {{ $tournament['competition_system_label'] ?? 'Sistem Gugur (Single Elimination)' }}</span>
                    <span class="rounded-full bg-white/10 px-3 py-1">Peserta Klub: {{ count($clubs) }}</span>
                </div>
                <p class="mt-3 text-xs text-slate-300">{{ $tournament['competition_system_description'] ?? '' }}</p>
            </div>

            @if (! empty($tournament['hero_image_url']))
                <div class="overflow-hidden rounded-2xl border border-white/15">
                    <img src="{{ $tournament['hero_image_url'] }}" alt="{{ $tournament['name'] }}" class="h-52 w-full object-cover sm:h-64" />
                </div>
            @endif
        </div>
    </section>

    @if (! empty($tournament['competition_rounds']) && is_array($tournament['competition_rounds']))
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-900">Alur Sistem Pertandingan</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($tournament['competition_rounds'] as $round)
                    <article class="rounded-xl border border-slate-100 p-4">
                        <div class="text-sm font-bold text-slate-900">{{ $round['name'] ?? '-' }}</div>
                        <div class="mt-1 text-xs text-slate-500">Stage: {{ $round['stage'] ?? '-' }} • Bracket: {{ $round['bracket'] ?? '-' }}</div>
                        <div class="mt-2 text-xs text-slate-600">Jumlah pertandingan: {{ $round['matches_count'] ?? 0 }}</div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if (($standings['enabled'] ?? false) === true)
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-xl font-black text-slate-900">{{ $standings['title'] ?? 'Klasemen' }}</h2>
            @if (! empty($standings['tables']))
                <div class="mt-4 space-y-4">
                    @foreach ($standings['tables'] as $table)
                        <div class="rounded-xl border border-slate-100">
                            <div class="border-b border-slate-100 px-4 py-3 text-sm font-bold text-slate-800">{{ $table['name'] ?? 'Overall' }}</div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-3 py-2">#</th>
                                            <th class="px-3 py-2">Klub</th>
                                            <th class="px-3 py-2">M</th>
                                            <th class="px-3 py-2">W</th>
                                            <th class="px-3 py-2">D</th>
                                            <th class="px-3 py-2">L</th>
                                            <th class="px-3 py-2">PF</th>
                                            <th class="px-3 py-2">PA</th>
                                            <th class="px-3 py-2">Diff</th>
                                            <th class="px-3 py-2">Pts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse (($table['rows'] ?? []) as $row)
                                            <tr class="border-t border-slate-100">
                                                <td class="px-3 py-2 font-semibold text-slate-700">{{ $row['rank'] ?? '-' }}</td>
                                                <td class="px-3 py-2 font-semibold text-slate-900">{{ $row['club_name'] ?? '-' }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['played'] ?? 0 }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['win'] ?? 0 }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['draw'] ?? 0 }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['loss'] ?? 0 }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['points_for'] ?? 0 }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['points_against'] ?? 0 }}</td>
                                                <td class="px-3 py-2 text-slate-600">{{ $row['diff'] ?? 0 }}</td>
                                                <td class="px-3 py-2 font-bold text-slate-900">{{ $row['league_points'] ?? 0 }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="px-3 py-6 text-center text-sm text-slate-500">Belum ada hasil pertandingan untuk klasemen.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-3 rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">Klasemen akan tampil setelah pertandingan berstatus selesai.</div>
            @endif
        </section>
    @endif

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


