@extends('layouts.admin-panel', ['title' => 'Tambah Jadwal'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Jadwal',
        'description' => 'Tambahkan agenda resmi pertandingan.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.schedules.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-1">
            @csrf
            @include('admin.schedules.form-fields', ['tournaments' => $tournaments])
        </form>
    </section>
@endsection

