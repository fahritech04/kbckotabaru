@extends('layouts.public-site', ['title' => 'Turnamen - KBC Kotabaru'])

@section('content')
    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-black text-slate-900 sm:text-3xl">Daftar Turnamen</h1>
        <p class="mt-2 text-sm text-slate-500">Pantau musim, lokasi, dan status turnamen basket Kotabaru.</p>
    </section>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($tournaments as $tournament)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-orange-600">{{ $tournament['season'] }}</div>
                <h2 class="mt-2 text-xl font-black text-slate-900">{{ $tournament['name'] }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ $tournament['location'] }}</p>
                <p class="mt-3 line-clamp-2 text-sm text-slate-600">{{ $tournament['description'] ?? 'Deskripsi turnamen belum tersedia.' }}</p>
                <div class="mt-4 flex items-center justify-between">
                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold uppercase text-slate-600">{{ $tournament['status'] }}</span>
                    <a href="{{ route('tournaments.show', $tournament['id']) }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Detail</a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data turnamen.</div>
        @endforelse
    </section>
@endsection


