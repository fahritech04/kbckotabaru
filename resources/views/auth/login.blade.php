@extends('layouts.public-site', ['title' => 'Login - KBC Kotabaru'])

@section('content')
    <div class="mx-auto max-w-md surface-card p-8">
        <h1 class="text-2xl font-black text-slate-900">Login KBC Kotabaru</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk sebagai user atau admin untuk mengakses fitur.</p>

        <form action="{{ route('login.perform') }}" method="POST" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required />
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                <input type="password" name="password" required />
            </div>

            <button type="submit" class="btn-primary w-full">Login</button>
        </form>

        <p class="mt-4 text-sm text-slate-500">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-orange-600 hover:text-orange-700">Daftar sekarang</a>.</p>
    </div>
@endsection


