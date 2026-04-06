@extends('layouts.admin', ['title' => 'Tambah Jadwal'])

@section('content')
    @include('admin.partials.page-header', [
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
            @include('admin.schedules._form', ['tournaments' => $tournaments])
        </form>
    </section>
@endsection
