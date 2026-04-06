@extends('layouts.admin-panel', ['title' => 'Edit Pertandingan'])

@section('content')
    @include('admin.components.page-header', [
        'title' => 'Edit Pertandingan',
        'description' => 'Perbarui skor, jadwal, dan status pertandingan.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.matches.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.matches.update', $match['id']) }}" method="POST" class="space-y-1">
            @csrf
            @method('PUT')
            @include('admin.matches.form-fields', ['match' => $match, 'tournaments' => $tournaments, 'clubs' => $clubs, 'schedules' => $schedules])
        </form>
    </section>
@endsection

