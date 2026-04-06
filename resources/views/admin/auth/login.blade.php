<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - KBC Kotabaru</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="flex min-h-screen items-center justify-center px-4 py-6 sm:py-10">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-xl bg-slate-900 text-lg font-black text-white">KBC</div>
                <h1 class="mt-4 text-2xl font-black text-slate-900">Admin Login</h1>
                <p class="mt-1 text-sm text-slate-500">Halaman ini khusus administrator KBC Kotabaru.</p>
            </div>

            @include('partials.alerts')

            <form action="{{ route('admin.login.perform') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Email Admin</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-orange-200 focus:ring" />
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-700">Password</label>
                    <input type="password" name="password" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none ring-orange-200 focus:ring" />
                </div>

                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white hover:bg-slate-700">Masuk Admin</button>
            </form>
        </div>
    </div>
</body>

</html>
