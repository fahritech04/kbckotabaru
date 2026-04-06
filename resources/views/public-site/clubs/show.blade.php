@extends('layouts.public-site', ['title' => $club['name'].' - KBC Kotabaru'])

@section('content')
    <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-900 sm:text-4xl">{{ $club['name'] }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $club['city'] ?? 'Kotabaru' }} • Coach {{ $club['coach'] ?? '-' }} • {{ $club['tournament']['name'] ?? '-' }}</p>
            </div>
            <div class="rounded-2xl bg-slate-900 px-5 py-3 text-center text-white">
                <div class="text-xs uppercase tracking-wide text-slate-300">Record</div>
                <div class="text-3xl font-black">{{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</div>
            </div>
        </div>

        @if ($clubLogoUrl)
            <img src="{{ $clubLogoUrl }}" alt="{{ $club['name'] }}" class="mt-5 h-48 w-full rounded-2xl object-cover sm:h-60" />
        @endif

        <p class="mt-6 text-sm text-slate-700">{{ $club['description'] ?? 'Belum ada deskripsi klub.' }}</p>
    </section>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-xl font-black text-slate-900">Roster Peserta</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($players as $player)
                <article class="rounded-xl border border-slate-100 p-4">
                    @if (! empty($player['photo_url']))
                        <img src="{{ \Illuminate\Support\Str::startsWith($player['photo_url'], ['http://', 'https://']) ? $player['photo_url'] : \Illuminate\Support\Facades\Storage::url($player['photo_url']) }}" alt="{{ $player['name'] }}" class="h-36 w-full rounded-lg object-cover" />
                    @endif
                    <div class="mt-3 text-xs font-semibold uppercase text-slate-500">No. {{ $player['jersey_number'] ?? '-' }}</div>
                    <div class="text-base font-black text-slate-900">{{ $player['name'] }}</div>
                    <div class="text-xs text-slate-500">Peserta Klub</div>
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Belum ada peserta pemain dari klub ini.</div>
            @endforelse
        </div>
    </section>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-xl font-black text-slate-900">Pertandingan Klub</h2>
        <div class="mt-4 space-y-3">
            @forelse ($matches as $match)
                <a href="{{ route('matches.show', $match['id']) }}" class="block rounded-xl border border-slate-100 p-4 hover:border-sky-200 hover:bg-sky-50/40">
                    <div class="text-xs text-slate-500">{{ $match['round'] ?? '-' }} • {{ $match['tipoff_at'] ?? '-' }}</div>
                    <div class="mt-2 flex flex-col items-center gap-2 text-sm font-bold text-slate-900 sm:flex-row sm:justify-between sm:gap-3">
                        <span>{{ $match['home_club']['name'] ?? '-' }}</span>
                        <span class="rounded bg-slate-900 px-2 py-1 text-xs text-white">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</span>
                        <span>{{ $match['away_club']['name'] ?? '-' }}</span>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500">Belum ada pertandingan untuk klub ini.</div>
            @endforelse
        </div>
    </section>
@endsection


