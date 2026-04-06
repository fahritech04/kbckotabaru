@extends('layouts.admin', ['title' => 'Tambah Jadwal'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Tambah Jadwal</h1>
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="mt-6">
            @csrf
            @include('admin.schedules._form', ['tournaments' => $tournaments])
        </form>
    </section>
@endsection

