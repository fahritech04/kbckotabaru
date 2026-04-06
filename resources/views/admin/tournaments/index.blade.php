@extends('layouts.admin-panel', ['title' => 'Admin Turnamen'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Manajemen Turnamen',
        'description' => 'Kelola data turnamen yang tampil di website user.',
        'primaryAction' => [
            'label' => 'Tambah Turnamen',
            'url' => route('admin.tournaments.create'),
            'class' => 'btn-accent',
        ],
    ])

    <div class="mt-6 space-y-3 md:hidden">
        @forelse ($tournaments as $tournament)
            <article class="surface-card p-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-orange-600">{{ $tournament['season'] }}</div>
                <h2 class="mt-1 text-base font-black text-slate-900">{{ $tournament['name'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $tournament['location'] }}</p>
                <span class="badge mt-3">{{ $tournament['status'] }}</span>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.tournaments.edit', $tournament['id']) }}" class="btn-secondary w-full px-3 py-2 text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament['id']) }}" onsubmit="return confirm('Hapus turnamen ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full px-3 py-2 text-xs">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="surface-card border-dashed p-8 text-center text-sm text-slate-500">Belum ada data turnamen.</div>
        @endforelse
    </div>

    <div class="mt-6 table-shell hidden md:block">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Season</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tournaments as $tournament)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $tournament['name'] }}</td>
                        <td class="text-slate-600">{{ $tournament['season'] }}</td>
                        <td class="text-slate-600">{{ $tournament['location'] }}</td>
                        <td><span class="badge">{{ $tournament['status'] }}</span></td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.tournaments.edit', $tournament['id']) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament['id']) }}" onsubmit="return confirm('Hapus turnamen ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-500">Belum ada data turnamen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

