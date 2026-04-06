@extends('layouts.guest', ['title' => 'Daftar Klub - KBC Kotabaru'])

@section('content')
    <div class="mx-auto w-full max-w-3xl">
        <h1 class="text-3xl font-black text-slate-900 sm:text-4xl">Pendaftaran Klub Turnamen</h1>
        <p class="mt-2 text-sm text-slate-500">Langkah 1: login Google dulu. Langkah 2: isi form data klub lengkap (tanpa password).</p>

        <div class="mt-6 grid gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-[1fr_auto] sm:items-center">
            <div>
                <div class="text-sm font-bold text-slate-800">Autentikasi Wajib Dengan Google</div>
                <p class="mt-1 text-sm text-slate-500">Setelah login berhasil, Anda akan diarahkan otomatis ke form data klub dan peserta.</p>
            </div>
            @if ($googleConfigured)
                <a href="{{ route('club.google.redirect') }}" class="inline-flex items-center justify-center gap-3 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-bold text-slate-800 transition hover:-translate-y-0.5 hover:bg-slate-100">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.7 32.7 29.3 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.1 6.1 29.3 4 24 4 12.9 4 4 12.9 4 24s8.9 20 20 20 20-8.9 20-20c0-1.2-.1-2.4-.4-3.5z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15 18.9 12 24 12c3 0 5.8 1.1 7.9 3l5.7-5.7C34.1 6.1 29.3 4 24 4c-7.7 0-14.3 4.4-17.7 10.7z"/>
                        <path fill="#4CAF50" d="M24 44c5.2 0 10-2 13.5-5.2l-6.2-5.2c-2.1 1.6-4.6 2.4-7.3 2.4-5.2 0-9.6-3.3-11.1-8l-6.5 5C9.8 39.3 16.4 44 24 44z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.1 5.6l.1-.1 6.2 5.2C37 38.3 44 33 44 24c0-1.2-.1-2.4-.4-3.5z"/>
                    </svg>
                    Login Google Klub
                </a>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                    Google Login belum aktif di `.env`.
                </div>
            @endif
        </div>

        <p class="mt-4 text-sm text-slate-500">Sudah pernah daftar? <a href="{{ route('club.login') }}" class="font-semibold text-slate-700 hover:text-slate-900">Masuk klub</a></p>
    </div>
@endsection
