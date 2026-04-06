@extends('layouts.club-portal', ['title' => 'Onboarding Klub'])

@section('content')
    @php
        $oldPlayers = old('players', []);
    @endphp

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-black text-slate-900">Lengkapi Data Klub</h1>
        <p class="mt-1 text-sm text-slate-500">Isi data resmi klub dan peserta. Maksimal 15 peserta, dan semua baris peserta boleh dikosongkan dulu.</p>
        <p class="mt-2 text-xs text-slate-400">Email login Google: {{ $authUser['email'] ?? '-' }}</p>

        <form method="POST" action="{{ route('club.onboarding.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Penanggung Jawab</label>
                    <input type="text" name="manager_name" value="{{ old('manager_name', $authUser['name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP Penanggung Jawab</label>
                    <input type="text" name="manager_phone" value="{{ old('manager_phone') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Email Klub</label>
                    <input type="email" name="club_email" value="{{ old('club_email', $authUser['email'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Klub</label>
                    <input type="text" name="club_name" value="{{ old('club_name') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Coach</label>
                    <input type="text" name="coach" value="{{ old('coach') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Pilih Event Turnamen</label>
                    <select name="tournament_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                        <option value="">Pilih turnamen</option>
                        @foreach ($tournaments as $tournament)
                            <option value="{{ $tournament['id'] }}" @selected(old('tournament_id') === $tournament['id'])>{{ $tournament['name'] }} ({{ $tournament['season'] }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar Klub</label>
                    <input type="file" name="club_logo" accept="image/*" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">KTP Coach</label>
                    <input type="file" name="coach_ktp" accept="image/*" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                <h2 class="text-lg font-black text-slate-900">Data Peserta (Opsional)</h2>
                <p class="mt-1 text-sm text-slate-500">Isi sesuai kebutuhan. Baris kosong tidak akan disimpan.</p>

                <div class="mt-4 space-y-3">
                    @for ($i = 0; $i < 15; $i++)
                        @php
                            $player = $oldPlayers[$i] ?? [];
                        @endphp
                        <div class="rounded-xl border border-slate-200 bg-white p-3">
                            <div class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Peserta {{ $i + 1 }}</div>
                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                                <div class="xl:col-span-2">
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nama Peserta</label>
                                    <input type="text" name="players[{{ $i }}][name]" value="{{ $player['name'] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Nomor Punggung</label>
                                    <input type="number" name="players[{{ $i }}][jersey_number]" min="0" value="{{ $player['jersey_number'] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">Foto Peserta</label>
                                    <input type="file" name="players[{{ $i }}][photo]" accept="image/*" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs" />
                                </div>
                                <div class="md:col-span-2 xl:col-span-4">
                                    <label class="mb-1 block text-xs font-semibold text-slate-700">KTP Peserta</label>
                                    <input type="file" name="players[{{ $i }}][ktp_image]" accept="image/*" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs" />
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white sm:w-auto">Simpan Data Klub</button>
        </form>
    </section>
@endsection

