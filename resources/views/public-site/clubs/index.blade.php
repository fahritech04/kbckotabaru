@extends('layouts.public-site', ['title' => 'Klub - KBC Kotabaru'])

@section('content')
    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
        <div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 sm:text-3xl">Daftar Klub</h1>
                <p class="mt-2 text-sm text-slate-500">Profil klub peserta kompetisi basket Kotabaru.</p>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($clubs as $club)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                @if (! empty($club['logo_url']))
                    <img src="{{ \Illuminate\Support\Str::startsWith($club['logo_url'], ['http://', 'https://']) ? $club['logo_url'] : \Illuminate\Support\Facades\Storage::url($club['logo_url']) }}" alt="{{ $club['name'] }}" class="mb-4 h-40 w-full rounded-xl object-cover" />
                @endif
                <div class="flex items-start justify-between gap-3">
                    <h2 class="text-xl font-black text-slate-900">{{ $club['name'] }}</h2>
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">{{ $club['city'] ?? 'Kotabaru' }}</p>
                <p class="mt-1 text-xs text-slate-400">Coach: {{ $club['coach'] ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-400">Turnamen: {{ $club['tournament']['name'] ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-400">Peserta: {{ $club['players_count'] ?? 0 }}</p>
                <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ $club['description'] ?? 'Belum ada deskripsi klub.' }}</p>
                <a href="{{ route('clubs.show', $club['id']) }}" class="mt-4 inline-flex text-sm font-semibold text-orange-600 hover:text-orange-700">Lihat profil</a>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data klub.</div>
        @endforelse
    </section>
@endsection

