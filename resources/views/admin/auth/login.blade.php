@extends('layouts.guest', ['title' => 'Login Admin - KBC Kotabaru'])

@section('content')
    <div class="mx-auto w-full max-w-md">
        <div class="mb-6 text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-900 text-lg font-black text-white">KBC</div>
            <h1 class="mt-4 text-3xl font-black text-slate-900">Admin Login</h1>
            <p class="mt-2 text-sm text-slate-500">Halaman ini khusus administrator KBC Kotabaru.</p>
        </div>

        <form action="{{ route('admin.login.perform') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" required />
            </div>

            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                <input type="password" name="password" required />
            </div>

            <button type="submit" class="btn-primary w-full">Masuk Admin</button>
        </form>
    </div>
@endsection
