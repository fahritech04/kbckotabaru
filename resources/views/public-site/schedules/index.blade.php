@extends('layouts.public-site', ['title' => 'Jadwal - KBC Kotabaru'])

@section('content')
    <section class="mb-6 rounded-2xl bg-white p-5 shadow-sm sm:p-6">
        <h1 class="text-2xl font-black text-slate-900 sm:text-3xl">Jadwal Kompetisi</h1>
        <p class="mt-2 text-sm text-slate-500">Agenda resmi event dan game day KBC Kotabaru.</p>
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="space-y-3 p-4 md:hidden">
            @forelse ($schedules as $schedule)
                <article class="rounded-xl border border-slate-100 p-4">
                    <div class="text-xs text-slate-500">{{ ! empty($schedule['scheduled_at']) ? \Illuminate\Support\Carbon::parse($schedule['scheduled_at'])->format('d M Y, H:i') : '-' }}</div>
                    <div class="mt-1 text-sm font-bold text-slate-900">{{ $schedule['title'] }}</div>
                    <div class="mt-1 text-xs text-slate-500">{{ $schedule['tournament']['name'] ?? '-' }} • {{ $schedule['venue'] }}</div>
                    <span class="mt-3 inline-flex rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $schedule['status'] }}</span>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">Belum ada data jadwal.</div>
            @endforelse
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-100 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Turnamen</th>
                        <th class="px-4 py-3">Venue</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr class="border-t border-slate-100">
                            <td class="px-4 py-3 text-slate-600">{{ ! empty($schedule['scheduled_at']) ? \Illuminate\Support\Carbon::parse($schedule['scheduled_at'])->format('d M Y, H:i') : '-' }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $schedule['title'] }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $schedule['tournament']['name'] ?? '-' }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $schedule['venue'] }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-bold text-slate-600">{{ $schedule['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada data jadwal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection


