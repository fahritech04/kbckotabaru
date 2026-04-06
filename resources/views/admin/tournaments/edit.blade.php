@extends('layouts.admin', ['title' => 'Edit Turnamen'])

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Edit Turnamen',
        'description' => 'Perbarui data turnamen secara lengkap.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.tournaments.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.tournaments.update', $tournament['id']) }}" method="POST" class="space-y-1">
            @csrf
            @method('PUT')
            @include('admin.tournaments._form', ['tournament' => $tournament])
        </form>
    </section>
@endsection
