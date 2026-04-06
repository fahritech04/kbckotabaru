@php
    $isEdit = isset($club);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Klub</label>
        <input type="text" name="name" value="{{ old('name', $club['name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Event Turnamen</label>
        <select name="tournament_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Pilih turnamen</option>
            @foreach ($tournaments as $tournament)
                <option value="{{ $tournament['id'] }}" @selected(old('tournament_id', $club['tournament_id'] ?? '') === $tournament['id'])>{{ $tournament['name'] }} ({{ $tournament['season'] }})</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Kota</label>
        <input type="text" name="city" value="{{ old('city', $club['city'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Coach</label>
        <input type="text" name="coach" value="{{ old('coach', $club['coach'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Penanggung Jawab</label>
        <input type="text" name="manager_name" value="{{ old('manager_name', $club['manager_name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Email Penanggung Jawab</label>
        <input type="email" name="manager_email" value="{{ old('manager_email', $club['manager_email'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Email Klub</label>
        <input type="email" name="club_email" value="{{ old('club_email', $club['club_email'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP Penanggung Jawab</label>
        <input type="text" name="manager_phone" value="{{ old('manager_phone', $club['manager_phone'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Tahun Berdiri</label>
        <input type="number" name="founded_year" value="{{ old('founded_year', $club['founded_year'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Win</label>
        <input type="number" name="wins" value="{{ old('wins', $club['wins'] ?? 0) }}" min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Loss</label>
        <input type="number" name="losses" value="{{ old('losses', $club['losses'] ?? 0) }}" min="0" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Upload Logo Klub</label>
        <input type="file" name="logo_file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Logo URL (opsional)</label>
        <input type="text" name="logo_url" value="{{ old('logo_url', $club['logo_url'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Upload KTP Coach</label>
        <input type="file" name="coach_ktp_file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">KTP Coach URL (opsional)</label>
        <input type="text" name="coach_ktp_url" value="{{ old('coach_ktp_url', $club['coach_ktp_url'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

@if (! empty($clubLogoUrl ?? null))
    <img src="{{ $clubLogoUrl }}" alt="Logo klub" class="mt-3 h-24 w-24 rounded-xl object-cover" />
@endif

@if (! empty($club['coach_ktp_url'] ?? null))
    <img src="{{ \Illuminate\Support\Str::startsWith($club['coach_ktp_url'], ['http://', 'https://']) ? $club['coach_ktp_url'] : \Illuminate\Support\Facades\Storage::url($club['coach_ktp_url']) }}" alt="KTP coach" class="mt-3 h-24 w-24 rounded-xl object-cover" />
@endif

<div class="mt-4">
    <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('description', $club['description'] ?? '') }}</textarea>
</div>

@php
    $existingPlayers = collect($players ?? [])->values()->all();
    $existingPlayersById = collect($existingPlayers)->keyBy('id');
    $defaultRows = [];

    for ($i = 0; $i < 15; $i++) {
        $existing = $existingPlayers[$i] ?? [];
        $defaultRows[$i] = [
            'id' => $existing['id'] ?? '',
            'name' => $existing['name'] ?? '',
            'jersey_number' => $existing['jersey_number'] ?? '',
        ];
    }

    $formPlayers = old('players', $defaultRows);
@endphp
<div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
    <h2 class="text-lg font-black text-slate-900">Data Peserta Klub</h2>
    <p class="mt-1 text-sm text-slate-500">Edit data peserta langsung di form ini. Untuk hapus peserta, kosongkan nama dan nomor punggung pada baris peserta tersebut lalu simpan.</p>

    <div class="mt-4 space-y-3">
        @for ($i = 0; $i < 15; $i++)
            @php
                $player = $formPlayers[$i] ?? [];
                $playerId = (string) ($player['id'] ?? '');
                $existingPlayer = $playerId !== '' ? $existingPlayersById->get($playerId) : null;
                $photoUrl = null;
                $ktpUrl = null;

                if ($existingPlayer !== null) {
                    if (! empty($existingPlayer['photo_url'])) {
                        $photoUrl = \Illuminate\Support\Str::startsWith($existingPlayer['photo_url'], ['http://', 'https://'])
                            ? $existingPlayer['photo_url']
                            : \Illuminate\Support\Facades\Storage::url($existingPlayer['photo_url']);
                    }
                    if (! empty($existingPlayer['ktp_url'])) {
                        $ktpUrl = \Illuminate\Support\Str::startsWith($existingPlayer['ktp_url'], ['http://', 'https://'])
                            ? $existingPlayer['ktp_url']
                            : \Illuminate\Support\Facades\Storage::url($existingPlayer['ktp_url']);
                    }
                }
            @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <div class="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Peserta {{ $i + 1 }}</div>
                <input type="hidden" name="players[{{ $i }}][id]" value="{{ $playerId }}" />

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
                        @if ($photoUrl)
                            <img src="{{ $photoUrl }}" alt="Foto peserta" class="mt-2 h-16 w-16 rounded-md object-cover" />
                        @endif
                    </div>
                    <div class="md:col-span-2 xl:col-span-4">
                        <label class="mb-1 block text-xs font-semibold text-slate-700">KTP Peserta</label>
                        <input type="file" name="players[{{ $i }}][ktp_image]" accept="image/*" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs" />
                        @if ($ktpUrl)
                            <img src="{{ $ktpUrl }}" alt="KTP peserta" class="mt-2 h-16 w-24 rounded-md object-cover" />
                        @endif
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('admin.clubs.index') }}" class="btn-secondary w-full sm:w-auto">Batal</a>
    <button type="submit" class="btn-primary w-full sm:w-auto">{{ $isEdit ? 'Update Klub' : 'Simpan Klub' }}</button>
</div>
