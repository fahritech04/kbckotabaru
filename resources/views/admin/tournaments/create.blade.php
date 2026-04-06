@extends('layouts.admin', ['title' => 'Tambah Turnamen'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Tambah Turnamen</h1>
        <form action="{{ route('admin.tournaments.store') }}" method="POST" class="mt-6">
            @csrf
            @include('admin.tournaments._form')
        </form>
    </section>
@endsection

