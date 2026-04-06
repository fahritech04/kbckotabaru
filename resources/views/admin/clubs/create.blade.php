@extends('layouts.admin-panel', ['title' => 'Tambah Klub'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Klub',
        'description' => 'Isi data klub baru untuk ditampilkan di website user.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.clubs.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-1">
            @csrf
            @include('admin.clubs.form-fields', ['tournaments' => $tournaments])
        </form>
    </section>
@endsection

