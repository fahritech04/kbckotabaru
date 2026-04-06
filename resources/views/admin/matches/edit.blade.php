@extends('layouts.admin', ['title' => 'Edit Pertandingan'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Edit Pertandingan</h1>
        <form action="{{ route('admin.matches.update', $match['id']) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')
            @include('admin.matches._form', ['match' => $match, 'tournaments' => $tournaments, 'clubs' => $clubs, 'schedules' => $schedules])
        </form>
    </section>
@endsection

