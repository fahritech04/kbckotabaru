@extends('layouts.admin-panel', ['title' => 'Tambah Turnamen'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Tambah Turnamen',
        'description' => 'Tambahkan turnamen baru untuk musim berjalan.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.tournaments.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.tournaments.store') }}" method="POST" class="space-y-1">
            @csrf
            @include('admin.tournaments.form-fields')
        </form>
    </section>
@endsection

