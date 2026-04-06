@extends('layouts.admin', ['title' => 'Admin Turnamen'])

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Turnamen</h1>
            <p class="text-sm text-slate-500">CRUD data turnamen yang tampil di website user.</p>
        </div>
        <a href="{{ route('admin.tournaments.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Tambah Turnamen</a>
    </div>

    <div class="space-y-3 md:hidden">
        @forelse ($tournaments as $tournament)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-orange-600">{{ $tournament['season'] }}</div>
                <h2 class="mt-1 text-base font-black text-slate-900">{{ $tournament['name'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $tournament['location'] }}</p>
                <span class="mt-3 inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-bold">{{ $tournament['status'] }}</span>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('admin.tournaments.edit', $tournament['id']) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-700">Edit</a>
                    <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament['id']) }}" class="flex-1" onsubmit="return confirm('Hapus turnamen ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data turnamen.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Season</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tournaments as $tournament)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $tournament['name'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $tournament['season'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $tournament['location'] }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold">{{ $tournament['status'] }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.tournaments.edit', $tournament['id']) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament['id']) }}" onsubmit="return confirm('Hapus turnamen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-bold text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data turnamen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
