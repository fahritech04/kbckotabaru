@php
    $isEdit = isset($player);
@endphp

<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Peserta</label>
        <input type="text" name="name" value="{{ old('name', $player['name'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor Punggung</label>
        <input type="number" min="0" name="jersey_number" value="{{ old('jersey_number', $player['jersey_number'] ?? '') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />
    </div>
</div>

<div class="mt-4 grid gap-4 md:grid-cols-2">
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar Peserta</label>
        <input type="file" name="photo" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />

        @if (! empty($playerPhotoUrl ?? null))
            <img src="{{ $playerPhotoUrl }}" alt="Foto pemain" class="mt-3 h-28 w-28 rounded-xl object-cover" />
        @endif
    </div>
    <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700">Gambar KTP Peserta</label>
        <input type="file" name="ktp_image" accept="image/*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm" />

        @if (! empty($playerKtpUrl ?? null))
            <img src="{{ $playerKtpUrl }}" alt="KTP pemain" class="mt-3 h-28 w-28 rounded-xl object-cover" />
        @endif
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
    <a href="{{ route('club.players.index') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2 text-center text-sm font-semibold text-slate-700 sm:w-auto">Batal</a>
    <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white sm:w-auto">{{ $isEdit ? 'Update Pemain' : 'Simpan Pemain' }}</button>
</div>
