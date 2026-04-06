@extends('layouts.admin-panel', ['title' => 'Tambah Pertandingan'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Pertandingan',
        'description' => 'Buat data pertandingan baru beserta skor awal.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.matches.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.matches.store') }}" method="POST" class="space-y-1">
            @csrf
            @include('admin.matches.form-fields', ['tournaments' => $tournaments, 'clubs' => $clubs, 'schedules' => $schedules])
        </form>
    </section>
@endsection

