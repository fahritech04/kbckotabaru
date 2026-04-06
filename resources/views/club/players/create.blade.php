@extends('layouts.club-portal', ['title' => 'Tambah Pemain'])

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-black text-slate-900">Tambah Pemain Klub</h1>
        <p class="mt-1 text-sm text-slate-500">Isi data peserta, upload foto, dan upload KTP peserta.</p>

        <form method="POST" action="{{ route('club.players.store') }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            @include('club.players.form-fields')
        </form>
    </section>
@endsection

