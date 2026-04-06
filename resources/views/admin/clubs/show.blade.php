@extends('layouts.admin', ['title' => 'Detail Klub'])

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Detail Klub</h1>
            <p class="text-sm text-slate-500">Informasi pendaftaran klub dan roster peserta.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.clubs.edit', $club['id']) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">Edit Klub</a>
            <a href="{{ route('admin.clubs.index') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Kembali</a>
        </div>
    </div>

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="grid gap-6 lg:grid-cols-[1fr_220px]">
            <div class="space-y-2 text-sm text-slate-600">
                <p><span class="font-semibold text-slate-800">Nama Klub:</span> {{ $club['name'] }}</p>
                <p><span class="font-semibold text-slate-800">Kota:</span> {{ $club['city'] }}</p>
                <p><span class="font-semibold text-slate-800">Coach:</span> {{ $club['coach'] }}</p>
                <p><span class="font-semibold text-slate-800">Event Turnamen:</span> {{ $club['tournament']['name'] ?? '-' }}</p>
                <p><span class="font-semibold text-slate-800">Penanggung Jawab:</span> {{ $club['manager_name'] ?? '-' }}</p>
                <p><span class="font-semibold text-slate-800">Email Penanggung Jawab:</span> {{ $club['manager_email'] ?? '-' }}</p>
                <p><span class="font-semibold text-slate-800">Email Klub:</span> {{ $club['club_email'] ?? '-' }}</p>
                <p><span class="font-semibold text-slate-800">Nomor HP:</span> {{ $club['manager_phone'] ?? '-' }}</p>
                <p><span class="font-semibold text-slate-800">Record:</span> {{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</p>
                <p><span class="font-semibold text-slate-800">Jumlah Peserta:</span> {{ count($players) }}</p>
                <p><span class="font-semibold text-slate-800">Deskripsi:</span> {{ $club['description'] ?? '-' }}</p>
            </div>

            <div class="space-y-3">
                @if ($clubLogoUrl)
                    <img src="{{ $clubLogoUrl }}" alt="Logo klub" class="h-28 w-full rounded-xl object-cover" />
                @else
                    <div class="flex h-28 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">Belum ada logo klub</div>
                @endif

                @if (! empty($club['coach_ktp_url']))
                    <img src="{{ \Illuminate\Support\Str::startsWith($club['coach_ktp_url'], ['http://', 'https://']) ? $club['coach_ktp_url'] : \Illuminate\Support\Facades\Storage::url($club['coach_ktp_url']) }}" alt="KTP coach" class="h-28 w-full rounded-xl object-cover" />
                @else
                    <div class="flex h-28 items-center justify-center rounded-xl border border-dashed border-slate-300 text-sm text-slate-500">Belum ada KTP coach</div>
                @endif
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-xl font-black text-slate-900">Daftar Peserta Klub</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($players as $player)
                <article class="rounded-xl border border-slate-100 p-4">
                    @if (! empty($player['photo_url']))
                        <img src="{{ \Illuminate\Support\Str::startsWith($player['photo_url'], ['http://', 'https://']) ? $player['photo_url'] : \Illuminate\Support\Facades\Storage::url($player['photo_url']) }}" alt="{{ $player['name'] }}" class="h-24 w-full rounded-lg object-cover" />
                    @endif
                    <div class="mt-3 text-xs font-semibold uppercase text-slate-500">No. {{ $player['jersey_number'] ?? '-' }}</div>
                    <div class="text-sm font-bold text-slate-900">{{ $player['name'] }}</div>
                    <div class="mt-2 text-xs text-slate-500">KTP Peserta:</div>
                    @if (! empty($player['ktp_url']))
                        <img src="{{ \Illuminate\Support\Str::startsWith($player['ktp_url'], ['http://', 'https://']) ? $player['ktp_url'] : \Illuminate\Support\Facades\Storage::url($player['ktp_url']) }}" alt="KTP peserta" class="mt-1 h-16 w-full rounded-lg object-cover" />
                    @else
                        <div class="mt-1 rounded-lg border border-dashed border-slate-200 px-2 py-2 text-center text-[11px] text-slate-500">Belum upload</div>
                    @endif
                </article>
            @empty
                <div class="col-span-full rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Belum ada peserta pemain untuk klub ini.</div>
            @endforelse
        </div>
    </section>
@endsection
