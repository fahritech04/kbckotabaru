@extends('layouts.app', ['title' => 'Daftar - KBC Kotabaru'])

@section('content')
    <div class="mx-auto max-w-md rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-black text-slate-900">Daftar Akun User</h1>
        <p class="mt-1 text-sm text-slate-500">Buat akun untuk mengikuti update kompetisi basket Kotabaru.</p>

        <form action="{{ route('register.perform') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-orange-200 focus:ring" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-orange-200 focus:ring" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                <input type="password" name="password" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-orange-200 focus:ring" />
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-orange-200 focus:ring" />
            </div>

            <button type="submit" class="w-full rounded-xl bg-orange-500 px-4 py-3 text-sm font-bold text-white hover:bg-orange-600">Buat Akun</button>
        </form>

        <p class="mt-4 text-sm text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-slate-700 hover:text-slate-900">Login</a>.</p>
    </div>
@endsection

