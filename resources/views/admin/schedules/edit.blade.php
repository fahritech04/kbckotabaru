@extends('layouts.admin', ['title' => 'Edit Jadwal'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Edit Jadwal</h1>
        <form action="{{ route('admin.schedules.update', $schedule['id']) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')
            @include('admin.schedules._form', ['schedule' => $schedule, 'tournaments' => $tournaments])
        </form>
    </section>
@endsection

