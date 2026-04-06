@php
    $isEdit = isset($schedule);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Turnamen</label>
        <select name="tournament_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Pilih turnamen</option>
            @foreach ($tournaments as $tournament)
                <option value="{{ $tournament['id'] }}" @selected(old('tournament_id', $schedule['tournament_id'] ?? '') === $tournament['id'])>{{ $tournament['name'] }} ({{ $tournament['season'] }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
        <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            @foreach (['draft', 'published', 'completed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $schedule['status'] ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Judul Event</label>
        <input type="text" name="title" value="{{ old('title', $schedule['title'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Venue</label>
        <input type="text" name="venue" value="{{ old('venue', $schedule['venue'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

<div class="mt-4">
    <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal & Jam</label>
    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', isset($schedule['scheduled_at']) ? \Illuminate\Support\Carbon::parse($schedule['scheduled_at'])->format('Y-m-d\TH:i') : '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
</div>

<div class="mt-4">
    <label class="mb-2 block text-sm font-semibold text-slate-700">Catatan</label>
    <textarea name="notes" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('notes', $schedule['notes'] ?? '') }}</textarea>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('admin.schedules.index') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 sm:w-auto">Batal</a>
    <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white sm:w-auto">{{ $isEdit ? 'Update Jadwal' : 'Simpan Jadwal' }}</button>
</div>

