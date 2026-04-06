@extends('layouts.club-portal', ['title' => 'Dashboard Klub'])

@section('content')
    <div class="grid gap-6 lg:grid-cols-[1.35fr_1fr]">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h1 class="text-2xl font-black text-slate-900">Profil Klub</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola data klub, event turnamen, dan dokumen resmi.</p>

            <form method="POST" action="{{ route('club.profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Penanggung Jawab</label>
                        <input type="text" name="manager_name" value="{{ old('manager_name', $club['manager_name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP Penanggung Jawab</label>
                        <input type="text" name="manager_phone" value="{{ old('manager_phone', $club['manager_phone'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Email Klub</label>
                        <input type="email" name="club_email" value="{{ old('club_email', $club['club_email'] ?? $club['manager_email'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Klub</label>
                        <input type="text" name="name" value="{{ old('name', $club['name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Coach</label>
                        <input type="text" name="coach" value="{{ old('coach', $club['coach'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Pilih Event Turnamen</label>
                        <select name="tournament_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                            <option value="">Pilih turnamen</option>
                            @foreach ($tournaments as $tournament)
                                <option value="{{ $tournament['id'] }}" @selected(old('tournament_id', $club['tournament_id'] ?? '') === $tournament['id'])>{{ $tournament['name'] }} ({{ $tournament['season'] }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar Klub</label>
                        <input type="file" name="club_logo" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar KTP Coach</label>
                        <input type="file" name="coach_ktp" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi Klub</label>
                    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('description', $club['description'] ?? '') }}</textarea>
                </div>

                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white sm:w-auto">Simpan Perubahan Klub</button>
            </form>
        </section>

        <section class="space-y-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Ringkasan Klub</h2>
                @if ($clubLogoUrl)
                    <img src="{{ $clubLogoUrl }}" alt="Logo Klub" class="mt-4 h-28 w-28 rounded-xl object-cover" />
                @endif
                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <p><span class="font-semibold text-slate-800">Klub:</span> {{ $club['name'] }}</p>
                    <p><span class="font-semibold text-slate-800">Turnamen:</span> {{ $club['tournament']['name'] ?? '-' }}</p>
                    <p><span class="font-semibold text-slate-800">Jumlah Pemain:</span> {{ count($players) }}</p>
                </div>
                <a href="{{ route('club.players.index') }}" class="mt-4 inline-flex rounded-lg bg-orange-500 px-4 py-2 text-sm font-bold text-white hover:bg-orange-600">Kelola Pemain</a>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Dokumen Coach</h2>
                @if ($coachKtpUrl)
                    <img src="{{ $coachKtpUrl }}" alt="KTP Coach" class="mt-3 h-48 w-full rounded-xl object-cover" />
                @else
                    <p class="mt-3 text-sm text-slate-500">Belum ada dokumen KTP coach.</p>
                @endif
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Preview Roster</h2>
                <div class="mt-3 space-y-2">
                    @forelse ($players as $player)
                        <div class="rounded-lg border border-slate-100 px-3 py-2 text-sm">
                            <div class="font-semibold text-slate-800">#{{ $player['jersey_number'] ?? '-' }} - {{ $player['name'] }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada pemain terdaftar.</p>
                    @endforelse
                </div>
            </article>
        </section>
    </div>
@endsection

