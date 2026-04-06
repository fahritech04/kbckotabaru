@extends('layouts.admin', ['title' => 'Edit Turnamen'])

@section('content')
    <section class="rounded-2xl bg-white p-6 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Edit Turnamen</h1>
        <form action="{{ route('admin.tournaments.update', $tournament['id']) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')
            @include('admin.tournaments._form', ['tournament' => $tournament])
        </form>
    </section>
@endsection

