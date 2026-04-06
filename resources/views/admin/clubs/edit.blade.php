@extends('layouts.admin', ['title' => 'Edit Klub'])

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Edit Klub',
        'description' => 'Perbarui profil klub dan dokumen resmi.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.clubs.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.clubs.update', $club['id']) }}" method="POST" enctype="multipart/form-data" class="space-y-1">
            @csrf
            @method('PUT')
            @include('admin.clubs._form', ['club' => $club, 'tournaments' => $tournaments, 'clubLogoUrl' => $clubLogoUrl])
        </form>
    </section>
@endsection
