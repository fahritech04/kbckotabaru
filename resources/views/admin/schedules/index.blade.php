@extends('layouts.admin', ['title' => 'Admin Jadwal'])

@section('content')
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Jadwal</h1>
            <p class="text-sm text-slate-500">Kelola event dan agenda pertandingan.</p>
        </div>
        <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center justify-center rounded-xl bg-orange-500 px-4 py-2.5 text-sm font-bold text-white hover:bg-orange-600">Tambah Jadwal</a>
    </div>

    <div class="space-y-3 md:hidden">
        @forelse ($schedules as $schedule)
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-xs text-slate-500">{{ $schedule['scheduled_at'] ?? '-' }}</div>
                <h2 class="mt-1 text-base font-black text-slate-900">{{ $schedule['title'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $schedule['tournament']['name'] ?? '-' }}</p>
                <span class="mt-3 inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-bold">{{ $schedule['status'] }}</span>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('admin.schedules.edit', $schedule['id']) }}" class="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-center text-xs font-bold text-slate-700">Edit</a>
                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule['id']) }}" class="flex-1" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full rounded-lg bg-rose-500 px-3 py-2 text-xs font-bold text-white">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada data jadwal.</div>
        @endforelse
    </div>

    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm md:block">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Turnamen</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $schedule)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3 text-slate-600">{{ $schedule['scheduled_at'] ?? '-' }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $schedule['title'] }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $schedule['tournament']['name'] ?? '-' }}</td>
                        <td class="px-4 py-3"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold">{{ $schedule['status'] }}</span></td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.schedules.edit', $schedule['id']) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.schedules.destroy', $schedule['id']) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-rose-500 px-3 py-1.5 text-xs font-bold text-white">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
