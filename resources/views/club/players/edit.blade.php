@extends('layouts.club', ['title' => 'Edit Pemain'])

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-black text-slate-900">Edit Pemain Klub</h1>
        <p class="mt-1 text-sm text-slate-500">Perbarui data peserta, foto, dan KTP peserta.</p>

        <form method="POST" action="{{ route('club.players.update', $player['id']) }}" enctype="multipart/form-data" class="mt-6">
            @csrf
            @method('PUT')
            @include('club.players._form', ['player' => $player, 'playerPhotoUrl' => $playerPhotoUrl, 'playerKtpUrl' => $playerKtpUrl])
        </form>
    </section>
@endsection
