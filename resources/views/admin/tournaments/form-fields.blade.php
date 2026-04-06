@php
    $isEdit = isset($tournament);
    $systemVisibility = (array) ($systemFieldVisibilityMap ?? []);
    $systemRequired = (array) ($systemFieldRequiredMap ?? []);
    $currentHeroImagePath = old('hero_image', $tournament['hero_image'] ?? null);
    $currentHeroImageUrl = null;

    if (! empty($currentHeroImagePath)) {
        if (\Illuminate\Support\Str::startsWith($currentHeroImagePath, ['http://', 'https://', '/storage/'])) {
            $currentHeroImageUrl = $currentHeroImagePath;
        } else {
            $currentHeroImageUrl = \Illuminate\Support\Facades\Storage::url($currentHeroImagePath);
        }
    }
@endphp

<div
    data-tournament-system-form
    data-system-visibility='@json($systemVisibility)'
    data-system-required='@json($systemRequired)'
>
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
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Sistem Pertandingan</label>
            <select name="competition_system" required data-system-selector class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                @foreach (($systemOptions ?? []) as $system)
                    <option value="{{ $system['code'] }}" @selected(old('competition_system', $tournament['competition_system'] ?? 'single_elimination') === $system['code'])>
                        {{ $system['label'] }}
                    </option>
                @endforeach
            </select>
            <p class="mt-2 text-xs text-slate-500">Format turnamen akan membentuk alur jadwal/match otomatis sesuai sistem yang dipilih.</p>
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

    @php
        $settings = (array) old('competition_settings', $tournament['competition_settings'] ?? []);
    @endphp
    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div data-system-field="system_rounds">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Jumlah Ronde Sistem (Swiss/Ladder)</label>
            <input type="number" min="1" max="24" name="system_rounds" value="{{ old('system_rounds', $settings['rounds'] ?? 3) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
        </div>
        <div data-system-field="system_best_of">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Best Of (Series)</label>
            <select name="system_best_of" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
                @foreach ([3, 5, 7] as $bestOf)
                    <option value="{{ $bestOf }}" @selected((int) old('system_best_of', $settings['best_of'] ?? 3) === $bestOf)>Best of {{ $bestOf }}</option>
                @endforeach
            </select>
        </div>
        <div data-system-field="system_group_count">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Jumlah Grup (Group + Knockout)</label>
            <input type="number" min="2" max="8" name="system_group_count" value="{{ old('system_group_count', $settings['group_count'] ?? 2) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
        </div>
        <div data-system-field="system_qualifiers_per_group">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Lolos per Grup</label>
            <input type="number" min="1" max="8" name="system_qualifiers_per_group" value="{{ old('system_qualifiers_per_group', $settings['qualifiers_per_group'] ?? 2) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
        </div>
        <div data-system-field="system_play_in_slots">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Slot Tim Play-In</label>
            <input type="number" min="2" max="16" name="system_play_in_slots" value="{{ old('system_play_in_slots', $settings['play_in_slots'] ?? 4) }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
        </div>
    </div>

    <div class="mt-4">
        <label class="mb-2 block text-sm font-semibold text-slate-700">Hero Image</label>
        <input type="file" name="hero_image_file" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-slate-700" />
        <p class="mt-2 text-xs text-slate-500">Upload gambar banner turnamen (disarankan rasio 16:9 agar tampil rapi di halaman publik).</p>

        @if ($currentHeroImageUrl)
            <div class="mt-3 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                <img src="{{ $currentHeroImageUrl }}" alt="Hero turnamen" class="h-44 w-full object-cover sm:h-52" />
            </div>
        @endif
    </div>

    <div class="mt-4">
        <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
        <textarea name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">{{ old('description', $tournament['description'] ?? '') }}</textarea>
    </div>

    <label class="mt-4 flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
        <input type="hidden" name="auto_sync_system" value="0" />
        <input type="checkbox" name="auto_sync_system" value="1" @checked((int) old('auto_sync_system', 1) === 1) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900" />
        <span>Sinkronkan otomatis sistem turnamen setelah simpan (buat/refresh jadwal dan pertandingan berdasarkan klub peserta).</span>
    </label>

    <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
        <a href="{{ route('admin.tournaments.index') }}" class="btn-secondary w-full sm:w-auto">Batal</a>
        <button type="submit" class="btn-primary w-full sm:w-auto">{{ $isEdit ? 'Update Turnamen' : 'Simpan Turnamen' }}</button>
    </div>
</div>
