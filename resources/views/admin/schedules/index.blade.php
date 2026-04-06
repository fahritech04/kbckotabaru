@extends('layouts.admin', ['title' => 'Admin Jadwal'])

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Manajemen Jadwal',
        'description' => 'Kelola event dan agenda pertandingan.',
        'primaryAction' => [
            'label' => 'Tambah Jadwal',
            'url' => route('admin.schedules.create'),
            'class' => 'btn-accent',
        ],
    ])

    <div class="mt-6 space-y-3 md:hidden">
        @forelse ($schedules as $schedule)
            <article class="surface-card p-4">
                <div class="text-xs text-slate-500">{{ $schedule['scheduled_at'] ?? '-' }}</div>
                <h2 class="mt-1 text-base font-black text-slate-900">{{ $schedule['title'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $schedule['tournament']['name'] ?? '-' }}</p>
                <span class="badge mt-3">{{ $schedule['status'] }}</span>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.schedules.edit', $schedule['id']) }}" class="btn-secondary w-full px-3 py-2 text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule['id']) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full px-3 py-2 text-xs">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="surface-card border-dashed p-8 text-center text-sm text-slate-500">Belum ada data jadwal.</div>
        @endforelse
    </div>

    <div class="mt-6 table-shell hidden md:block">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Judul</th>
                    <th>Turnamen</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $schedule)
                    <tr>
                        <td class="text-slate-600">{{ $schedule['scheduled_at'] ?? '-' }}</td>
                        <td class="font-semibold text-slate-900">{{ $schedule['title'] }}</td>
                        <td class="text-slate-600">{{ $schedule['tournament']['name'] ?? '-' }}</td>
                        <td><span class="badge">{{ $schedule['status'] }}</span></td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.schedules.edit', $schedule['id']) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.schedules.destroy', $schedule['id']) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-500">Belum ada data jadwal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
