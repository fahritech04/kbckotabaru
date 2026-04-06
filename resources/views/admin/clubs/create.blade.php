@extends('layouts.admin', ['title' => 'Tambah Klub'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Tambah Klub</h1>
        <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data" class="mt-6">
            @csrf
            @include('admin.clubs._form', ['tournaments' => $tournaments])
        </form>
    </section>
@endsection

