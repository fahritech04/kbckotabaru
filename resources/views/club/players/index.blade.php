@extends('layouts.club', ['title' => 'Pemain Klub'])

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Roster Pemain {{ $club['name'] }}</h1>
            <p class="text-sm text-slate-500">Kelola nama peserta, nomor punggung, foto, dan KTP peserta.</p>
        </div>
        <a href="{{ route('club.players.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Tambah Pemain</a>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @forelse ($players as $player)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                @if (! empty($player['photo_url']))
                    <img src="{{ \Illuminate\Support\Str::startsWith($player['photo_url'], ['http://', 'https://']) ? $player['photo_url'] : \Illuminate\Support\Facades\Storage::url($player['photo_url']) }}" alt="{{ $player['name'] }}" class="h-36 w-full rounded-xl object-cover" />
                @endif
                <div class="mt-3 text-xs font-semibold uppercase text-slate-500">No. {{ $player['jersey_number'] ?? '-' }}</div>
                <h2 class="text-lg font-black text-slate-900">{{ $player['name'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">Dokumen KTP: {{ ! empty($player['ktp_url']) ? 'Tersedia' : 'Belum upload' }}</p>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('club.players.edit', $player['id']) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-700">Edit</a>
                    <form method="POST" action="{{ route('club.players.destroy', $player['id']) }}" class="flex-1" onsubmit="return confirm('Hapus pemain ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada pemain. Tambahkan roster klub sekarang.</div>
        @endforelse
    </div>
@endsection
