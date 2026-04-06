@extends('layouts.admin-panel', ['title' => 'Admin Klub'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Manajemen Klub',
        'description' => 'Kelola profil klub yang muncul di website user.',
        'primaryAction' => [
            'label' => 'Tambah Klub',
            'url' => route('admin.clubs.create'),
            'class' => 'btn-accent',
        ],
    ])

    <div class="mt-6 space-y-3 md:hidden">
        @forelse ($clubs as $club)
            <article class="surface-card p-4">
                <h2 class="text-base font-black text-slate-900">{{ $club['name'] }}</h2>
                <p class="mt-1 text-xs text-slate-500">{{ $club['city'] ?? '-' }} • Coach {{ $club['coach'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Penanggung Jawab: {{ $club['manager_name'] ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-500">Nomor HP: {{ $club['manager_phone'] ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-500">Email Klub: {{ $club['club_email'] ?? '-' }}</p>
                <p class="mt-1 text-xs text-slate-500">Turnamen: {{ $club['tournament']['name'] ?? '-' }}</p>
                <p class="mt-2 text-xs text-slate-600">Record: {{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</p>
                <p class="text-xs text-slate-600">Jumlah peserta: {{ $club['players_count'] ?? 0 }}</p>
                <div class="mt-4 grid grid-cols-3 gap-2">
                    <a href="{{ route('admin.clubs.show', $club['id']) }}" class="btn-secondary w-full px-3 py-2 text-xs">Detail</a>
                    <a href="{{ route('admin.clubs.edit', $club['id']) }}" class="btn-secondary w-full px-3 py-2 text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.clubs.destroy', $club['id']) }}" onsubmit="return confirm('Hapus klub ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full px-3 py-2 text-xs">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="surface-card border-dashed p-8 text-center text-sm text-slate-500">Belum ada data klub.</div>
        @endforelse
    </div>

    <div class="mt-6 table-shell hidden md:block">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Penanggung Jawab</th>
                    <th>Nomor HP</th>
                    <th>Email Klub</th>
                    <th>Kota</th>
                    <th>Coach</th>
                    <th>Turnamen</th>
                    <th>Peserta</th>
                    <th>Record</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clubs as $club)
                    <tr>
                        <td class="font-semibold text-slate-900">{{ $club['name'] }}</td>
                        <td class="text-slate-600">{{ $club['manager_name'] ?? '-' }}</td>
                        <td class="text-slate-600">{{ $club['manager_phone'] ?? '-' }}</td>
                        <td class="text-slate-600">{{ $club['club_email'] ?? '-' }}</td>
                        <td class="text-slate-600">{{ $club['city'] ?? '-' }}</td>
                        <td class="text-slate-600">{{ $club['coach'] }}</td>
                        <td class="text-slate-600">{{ $club['tournament']['name'] ?? '-' }}</td>
                        <td class="text-slate-600">{{ $club['players_count'] ?? 0 }}</td>
                        <td class="text-slate-600">{{ $club['wins'] ?? 0 }}-{{ $club['losses'] ?? 0 }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.clubs.show', $club['id']) }}" class="btn-secondary px-3 py-1.5 text-xs">Detail</a>
                                <a href="{{ route('admin.clubs.edit', $club['id']) }}" class="btn-secondary px-3 py-1.5 text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.clubs.destroy', $club['id']) }}" onsubmit="return confirm('Hapus klub ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-slate-500">Belum ada data klub.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

