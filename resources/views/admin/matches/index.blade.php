@extends('layouts.admin', ['title' => 'Admin Pertandingan'])

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Manajemen Pertandingan',
        'description' => 'Kelola hasil, skor, dan informasi laga.',
        'primaryAction' => [
            'label' => 'Tambah Pertandingan',
            'url' => route('admin.matches.create'),
            'class' => 'btn-accent',
        ],
    ])

    <div class="mt-6 space-y-3 md:hidden">
        @forelse ($matches as $match)
            <article class="surface-card p-4">
                <div class="text-xs text-slate-500">{{ $match['tipoff_at'] ?? '-' }}</div>
                <h2 class="mt-1 text-base font-black text-slate-900">{{ $match['home_club']['name'] ?? '-' }} vs {{ $match['away_club']['name'] ?? '-' }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $match['tournament']['name'] ?? '-' }}</p>
                <div class="mt-3 inline-flex rounded-lg bg-slate-900 px-3 py-1 text-sm font-black text-white">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</div>
                <div class="mt-3"><span class="badge">{{ $match['status'] }}</span></div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.matches.edit', $match['id']) }}" class="btn-secondary w-full px-3 py-2 text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.matches.destroy', $match['id']) }}" onsubmit="return confirm('Hapus pertandingan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full px-3 py-2 text-xs">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="surface-card border-dashed p-8 text-center text-sm text-slate-500">Belum ada data pertandingan.</div>
        @endforelse
    </div>

    <div class="mt-6 table-shell hidden md:block">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Pertandingan</th>
                    <th>Turnamen</th>
                    <th>Skor</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matches as $match)
                    <tr>
                        <td>
                            <div class="font-semibold text-slate-900">{{ $match['home_club']['name'] ?? '-' }} vs {{ $match['away_club']['name'] ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $match['tipoff_at'] ?? '-' }}</div>
                        </td>
                        <td class="text-slate-600">{{ $match['tournament']['name'] ?? '-' }}</td>
                        <td class="font-bold text-slate-900">{{ $match['home_score'] ?? 0 }} - {{ $match['away_score'] ?? 0 }}</td>
                        <td><span class="badge">{{ $match['status'] }}</span></td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.matches.edit', $match['id']) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.matches.destroy', $match['id']) }}" onsubmit="return confirm('Hapus pertandingan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-slate-500">Belum ada data pertandingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
