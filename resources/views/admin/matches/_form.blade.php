@php
    $isEdit = isset($match);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Turnamen</label>
        <select name="tournament_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Pilih turnamen</option>
            @foreach ($tournaments as $tournament)
                <option value="{{ $tournament['id'] }}" @selected(old('tournament_id', $match['tournament_id'] ?? '') === $tournament['id'])>{{ $tournament['name'] }} ({{ $tournament['season'] }})</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Jadwal (opsional)</label>
        <select name="schedule_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Tanpa jadwal</option>
            @foreach ($schedules as $schedule)
                <option value="{{ $schedule['id'] }}" @selected(old('schedule_id', $match['schedule_id'] ?? '') === $schedule['id'])>{{ $schedule['title'] }} - {{ $schedule['scheduled_at'] ?? '' }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Home Club</label>
        <select name="home_club_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Pilih klub</option>
            @foreach ($clubs as $club)
                <option value="{{ $club['id'] }}" @selected(old('home_club_id', $match['home_club_id'] ?? '') === $club['id'])>{{ $club['name'] }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Away Club</label>
        <select name="away_club_id" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            <option value="">Pilih klub</option>
            @foreach ($clubs as $club)
                <option value="{{ $club['id'] }}" @selected(old('away_club_id', $match['away_club_id'] ?? '') === $club['id'])>{{ $club['name'] }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Round</label>
        <input type="text" name="round" value="{{ old('round', $match['round'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
        <select name="status" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            @foreach (['scheduled', 'live', 'selesai', 'postponed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $match['status'] ?? 'scheduled') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Tipoff</label>
        <input type="datetime-local" name="tipoff_at" value="{{ old('tipoff_at', isset($match['tipoff_at']) ? \Illuminate\Support\Carbon::parse($match['tipoff_at'])->format('Y-m-d\TH:i') : '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Venue</label>
        <input type="text" name="venue" value="{{ old('venue', $match['venue'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Score Home</label>
        <input type="number" min="0" name="home_score" value="{{ old('home_score', $match['home_score'] ?? 0) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Score Away</label>
        <input type="number" min="0" name="away_score" value="{{ old('away_score', $match['away_score'] ?? 0) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

<div class="mt-4">
    <label class="mb-2 block text-sm font-semibold text-slate-700">Highlight</label>
    <textarea name="highlight" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('highlight', $match['highlight'] ?? '') }}</textarea>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('admin.matches.index') }}" class="btn-secondary w-full sm:w-auto">Batal</a>
    <button type="submit" class="btn-primary w-full sm:w-auto">{{ $isEdit ? 'Update Pertandingan' : 'Simpan Pertandingan' }}</button>
</div>


