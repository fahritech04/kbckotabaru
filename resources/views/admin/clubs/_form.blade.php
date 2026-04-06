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
        <select name="tournament_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Belum memilih turnamen</option>
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
        <input type="text" name="manager_name" value="{{ old('manager_name', $club['manager_name'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Email Penanggung Jawab</label>
        <input type="email" name="manager_email" value="{{ old('manager_email', $club['manager_email'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Email Klub</label>
        <input type="email" name="club_email" value="{{ old('club_email', $club['club_email'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP Penanggung Jawab</label>
        <input type="text" name="manager_phone" value="{{ old('manager_phone', $club['manager_phone'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
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

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('admin.clubs.index') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 sm:w-auto">Batal</a>
    <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white sm:w-auto">{{ $isEdit ? 'Update Klub' : 'Simpan Klub' }}</button>
</div>
