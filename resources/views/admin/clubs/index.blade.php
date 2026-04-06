@extends('layouts.admin', ['title' => 'Admin Klub'])

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Klub</h1>
            <p class="text-sm text-slate-500">Kelola profil klub yang muncul di website user.</p>
        </div>
        <a href="{{ route('admin.clubs.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Tambah Klub</a>
    </div>

    <div class="space-y-3 md:hidden">
        @forelse ($clubs as $club)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <h2 class="text-base font-black text-slate-900">{{ $club['name'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $club['city'] ?? '-' }} • Coach {{ $club['coach'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Email Klub: {{ $club['club_email'] ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-500">Turnamen: {{ $club['tournament']['name'] ?? '-' }}</p>
                <p class="mt-2 text-xs text-slate-600">Record: {{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</p>
                <p class="text-xs text-slate-600">Jumlah peserta: {{ $club['players_count'] ?? 0 }}</p>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('admin.clubs.show', $club['id']) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-700">Detail</a>
                    <a href="{{ route('admin.clubs.edit', $club['id']) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-700">Edit</a>
                    <form method="POST" action="{{ route('admin.clubs.destroy', $club['id']) }}" class="flex-1" onsubmit="return confirm('Hapus klub ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data klub.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Kota</th>
                    <th class="px-4 py-3">Coach</th>
                    <th class="px-4 py-3">Turnamen</th>
                    <th class="px-4 py-3">Peserta</th>
                    <th class="px-4 py-3">Record</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clubs as $club)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $club['name'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $club['city'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $club['coach'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $club['tournament']['name'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $club['players_count'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.clubs.show', $club['id']) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700">Detail</a>
                                <a href="{{ route('admin.clubs.edit', $club['id']) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.clubs.destroy', $club['id']) }}" onsubmit="return confirm('Hapus klub ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-bold text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada data klub.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
