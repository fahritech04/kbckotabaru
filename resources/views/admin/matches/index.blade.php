@extends('layouts.admin', ['title' => 'Admin Pertandingan'])

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Pertandingan</h1>
            <p class="text-sm text-slate-500">Kelola hasil, score, dan informasi laga.</p>
        </div>
        <a href="{{ route('admin.matches.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Tambah Pertandingan</a>
    </div>

    <div class="space-y-3 md:hidden">
        @forelse ($matches as $match)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">{{ $match['tipoff_at'] ?? '-' }}</div>
                <h2 class="mt-1 text-base font-black text-slate-900">{{ $match['home_club']['name'] ?? '-' }} vs {{ $match['away_club']['name'] ?? '-' }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $match['tournament']['name'] ?? '-' }}</p>
                <div class="mt-3 inline-flex rounded-lg bg-slate-900 px-3 py-1 text-sm font-black text-white">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</div>
                <div class="mt-3 text-xs font-semibold uppercase text-slate-500">{{ $match['status'] }}</div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('admin.matches.edit', $match['id']) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-700">Edit</a>
                    <form method="POST" action="{{ route('admin.matches.destroy', $match['id']) }}" class="flex-1" onsubmit="return confirm('Hapus pertandingan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data pertandingan.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Pertandingan</th>
                    <th class="px-4 py-3">Turnamen</th>
                    <th class="px-4 py-3">Score</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matches as $match)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900">{{ $match['home_club']['name'] ?? '-' }} vs {{ $match['away_club']['name'] ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $match['tipoff_at'] ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $match['tournament']['name'] ?? '-' }}</td>
                        <td class="px-4 py-3 font-bold text-slate-900">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold">{{ $match['status'] }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.matches.edit', $match['id']) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.matches.destroy', $match['id']) }}" onsubmit="return confirm('Hapus pertandingan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-bold text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data pertandingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
