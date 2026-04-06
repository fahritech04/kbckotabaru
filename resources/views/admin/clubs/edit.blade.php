@extends('layouts.admin', ['title' => 'Edit Klub'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Edit Klub</h1>
        <form action="{{ route('admin.clubs.update', $club['id']) }}" method="POST" enctype="multipart/form-data" class="mt-6">
            @csrf
            @method('PUT')
            @include('admin.clubs._form', ['club' => $club, 'tournaments' => $tournaments, 'clubLogoUrl' => $clubLogoUrl])
        </form>
    </section>
@endsection

