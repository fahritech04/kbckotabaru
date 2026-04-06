@extends('layouts.admin', ['title' => 'Tambah Turnamen'])

@section('content')
    @include('admin.partials.page-header', [
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
            @include('admin.tournaments._form')
        </form>
    </section>
@endsection
