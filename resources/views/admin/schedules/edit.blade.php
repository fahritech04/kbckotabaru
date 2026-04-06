@extends('layouts.admin', ['title' => 'Edit Jadwal'])

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'Edit Jadwal',
        'description' => 'Perbarui detail agenda pertandingan.',
        'secondaryAction' => [
            'label' => 'Kembali',
            'url' => route('admin.schedules.index'),
            'class' => 'btn-secondary',
        ],
    ])

    <section class="mt-6 surface-card p-6">
        <form action="{{ route('admin.schedules.update', $schedule['id']) }}" method="POST" class="space-y-1">
            @csrf
            @method('PUT')
            @include('admin.schedules._form', ['schedule' => $schedule, 'tournaments' => $tournaments])
        </form>
    </section>
@endsection
