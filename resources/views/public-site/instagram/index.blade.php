@extends('layouts.public-site', ['title' => 'Instagram - KBC Kotabaru'])

@section('content')
    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6">
        <div class="mb-4 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black text-slate-900 sm:text-3xl">Instagram Social Media</h1>
                <p class="mt-1 text-sm text-slate-500">Sorotan aktivitas sosial media terbaru.</p>
            </div>
        </div>

        @if (! empty($instagram))
            <div class="mb-4 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:p-4">
                <div class="flex flex-wrap items-center gap-3 sm:gap-5">
                    <img src="{{ ! empty($instagram['profile_pic_token']) ? route('instagram.media', $instagram['profile_pic_token']) : $instagram['profile_pic_url'] }}" alt="Foto profil Instagram" class="h-14 w-14 shrink-0 rounded-full border border-slate-200 object-cover sm:h-16 sm:w-16">
                    <div class="min-w-0 flex-1">
                        <a href="{{ $instagram['profile_url'] }}" target="_blank" rel="noopener noreferrer" class="line-clamp-1 text-sm font-black text-slate-900 sm:text-base">{{ $instagram['full_name'] }}</a>
                        <div class="line-clamp-1 text-xs text-slate-500 sm:text-sm">&#64;{{ $instagram['username'] }}</div>
                    </div>
                    <a href="{{ $instagram['profile_url'] }}" target="_blank" rel="noopener noreferrer" class="order-3 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-blue-700 bg-[#0095f6] px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#1877f2] sm:order-none sm:ml-auto sm:w-auto sm:text-sm">
                        <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current">
                            <path d="M7.8 2h8.4A5.8 5.8 0 0 1 22 7.8v8.4A5.8 5.8 0 0 1 16.2 22H7.8A5.8 5.8 0 0 1 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2Zm0 1.7A4.1 4.1 0 0 0 3.7 7.8v8.4a4.1 4.1 0 0 0 4.1 4.1h8.4a4.1 4.1 0 0 0 4.1-4.1V7.8a4.1 4.1 0 0 0-4.1-4.1H7.8Zm8.9 1.3a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4ZM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.7a3.3 3.3 0 1 0 0 6.6 3.3 3.3 0 0 0 0-6.6Z"></path>
                        </svg>
                        <span class="text-black">Follow</span>
                    </a>
                    <div class="order-4 grid w-full grid-cols-3 gap-3 text-center sm:order-none sm:w-auto sm:flex sm:gap-6">
                        <div>
                            <div class="text-sm font-black text-slate-900 sm:text-base">{{ number_format($instagram['posts_count']) }}</div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500 sm:text-xs">Posts</div>
                        </div>
                        <div>
                            <div class="text-sm font-black text-slate-900 sm:text-base">{{ number_format($instagram['followers']) }}</div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500 sm:text-xs">Followers</div>
                        </div>
                        <div>
                            <div class="text-sm font-black text-slate-900 sm:text-base">{{ number_format($instagram['following']) }}</div>
                            <div class="text-[11px] uppercase tracking-wide text-slate-500 sm:text-xs">Following</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3">
                @foreach ($instagram['items'] as $post)
                    <a href="{{ $post['permalink'] }}" target="_blank" rel="noopener noreferrer" class="group relative block aspect-square overflow-hidden rounded-xl bg-slate-100">
                        <img src="{{ ! empty($post['image_token']) ? route('instagram.media', $post['image_token']) : $post['image'] }}" alt="{{ $post['caption'] }}" loading="lazy" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/45 via-black/0 to-black/0 opacity-0 transition group-hover:opacity-100"></div>
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 flex items-center justify-between px-3 py-2 text-[11px] font-semibold text-white opacity-0 transition group-hover:opacity-100 sm:text-xs">
                            <span>&#10084; {{ number_format($post['likes']) }}</span>
                            <span>&#128172; {{ number_format($post['comments']) }}</span>
                        </div>
                        @if ($post['is_video'])
                            <div class="pointer-events-none absolute right-2 top-2 rounded-md bg-black/55 px-2 py-1 text-[10px] font-bold text-white sm:text-xs">VIDEO</div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                <p class="text-sm text-slate-600">Feed Instagram belum bisa dimuat saat ini.</p>
                <a href="https://www.instagram.com/kbccrewofficial/" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">
                    Lihat Instagram Resmi
                </a>
            </div>
        @endif
    </section>
@endsection
