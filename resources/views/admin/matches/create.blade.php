@extends('layouts.admin', ['title' => 'Tambah Pertandingan'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Tambah Pertandingan</h1>
        <form action="{{ route('admin.matches.store') }}" method="POST" class="mt-6">
            @csrf
            @include('admin.matches._form', ['tournaments' => $tournaments, 'clubs' => $clubs, 'schedules' => $schedules])
        </form>
    </section>
@endsection

