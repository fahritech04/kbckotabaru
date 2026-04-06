@php
    $isEdit = isset($tournament);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Turnamen</label>
        <input type="text" name="name" value="{{ old('name', $tournament['name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Season</label>
        <input type="text" name="season" value="{{ old('season', $tournament['season'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Lokasi</label>
        <input type="text" name="location" value="{{ old('location', $tournament['location'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
        <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            @foreach (['upcoming', 'ongoing', 'finished'] as $status)
                <option value="{{ $status }}" @selected(old('status', $tournament['status'] ?? 'upcoming') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Mulai</label>
        <input type="date" name="start_date" value="{{ old('start_date', $tournament['start_date'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Selesai</label>
        <input type="date" name="end_date" value="{{ old('end_date', $tournament['end_date'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

<div class="mt-4">
    <label class="mb-2 block text-sm font-semibold text-slate-700">Hero Image URL</label>
    <input type="url" name="hero_image" value="{{ old('hero_image', $tournament['hero_image'] ?? '') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
</div>

<div class="mt-4">
    <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
    <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('description', $tournament['description'] ?? '') }}</textarea>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('admin.tournaments.index') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 sm:w-auto">Batal</a>
    <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white sm:w-auto">{{ $isEdit ? 'Update Turnamen' : 'Simpan Turnamen' }}</button>
</div>

