<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Penanggung Jawab</label>
        <input type="text" name="manager_name" value="{{ old('manager_name') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP Penanggung Jawab</label>
        <input type="text" name="manager_phone" value="{{ old('manager_phone') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Email Klub</label>
        <input type="email" name="club_email" value="{{ old('club_email') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Klub</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
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

<div class="mt-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar Klub</label>
        <input type="file" name="logo_file" accept="image/*" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">KTP Coach</label>
        <input type="file" name="coach_ktp_file" accept="image/*" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

@php
    $oldPlayers = old('players', []);
@endphp
<div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
    <h2 class="text-lg font-black text-slate-900">Data Peserta (Opsional)</h2>
    <p class="mt-1 text-sm text-slate-500">Isi peserta klub jika sudah ada. Baris kosong tidak akan disimpan.</p>

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
                        <input type="number" min="0" name="players[{{ $i }}][jersey_number]" value="{{ $player['jersey_number'] ?? '' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm" />
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

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('admin.clubs.index') }}" class="btn-secondary w-full sm:w-auto">Batal</a>
    <button type="submit" class="btn-primary w-full sm:w-auto">Simpan Klub</button>
</div>
