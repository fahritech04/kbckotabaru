<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin KBC Kotabaru' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="m-0 min-h-screen bg-slate-100 text-slate-800">
    @include('shared.navigation.admin-panel')

    <div class="min-h-screen pt-[88px] md:ml-[290px] md:pt-0">
        <header class="border-b border-slate-200/80 bg-white/80 px-4 py-4 backdrop-blur sm:px-6">
            <div class="text-sm text-slate-500">Selamat datang, <span class="font-semibold text-slate-700">{{ $adminAuthUser['name'] ?? 'Admin' }}</span></div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            @include('shared.alerts')
            <div class="mx-auto w-full max-w-7xl">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>


