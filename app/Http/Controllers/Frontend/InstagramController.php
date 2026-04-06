<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class InstagramController extends Controller
{
    private const USERNAME = 'kbccrewofficial';

    private const API_APP_ID = '936619743392459';

    public function __invoke(): View
    {
        $instagram = Cache::remember(
            'public-site.instagram.feed.v2.'.self::USERNAME,
            now()->addMinutes(15),
            fn (): ?array => $this->fetchInstagramFeed()
        );

        return view('public-site.instagram.index', [
            'instagram' => $instagram,
        ]);
    }

    public function media(string $token): Response
    {
        $url = $this->decodeMediaToken($token);

        if (! is_string($url) || ! $this->isAllowedMediaHost($url)) {
            abort(404);
        }

        $payload = Cache::remember(
            'public-site.instagram.media.'.sha1($url),
            now()->addHours(6),
            function () use ($url): ?array {
                $response = Http::timeout(20)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; KBCKotabaruBot/1.0)',
                    ])
                    ->get($url);

                if (! $response->ok()) {
                    return null;
                }

                return [
                    'content_type' => (string) $response->header('Content-Type', 'image/jpeg'),
                    'body_base64' => base64_encode($response->body()),
                ];
            }
        );

        if (! is_array($payload)) {
            abort(404);
        }

        return response(base64_decode((string) $payload['body_base64']) ?: '', 200, [
            'Content-Type' => (string) $payload['content_type'],
            'Cache-Control' => 'public, max-age=21600',
        ]);
    }

    private function fetchInstagramFeed(): ?array
    {
        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->withHeaders([
                    'x-ig-app-id' => self::API_APP_ID,
                    'User-Agent' => 'Mozilla/5.0 (compatible; KBCKotabaruBot/1.0; +https://www.instagram.com/'.self::USERNAME.'/)',
                ])
                ->get('https://www.instagram.com/api/v1/users/web_profile_info/', [
                    'username' => self::USERNAME,
                ]);

            if (! $response->ok()) {
                return null;
            }

            $user = data_get($response->json(), 'data.user');

            if (! is_array($user)) {
                return null;
            }

            $items = collect(data_get($user, 'edge_owner_to_timeline_media.edges', []))
                ->map(function ($edge): ?array {
                    $node = data_get($edge, 'node');

                    if (! is_array($node)) {
                        return null;
                    }

                    $shortcode = data_get($node, 'shortcode');
                    $image = data_get($node, 'display_url') ?: data_get($node, 'thumbnail_src');

                    if (! is_string($shortcode) || ! is_string($image)) {
                        return null;
                    }

                    return [
                        'shortcode' => $shortcode,
                        'permalink' => 'https://www.instagram.com/p/'.$shortcode.'/',
                        'image' => $image,
                        'image_token' => $this->encodeMediaToken($image),
                        'caption' => data_get($node, 'edge_media_to_caption.edges.0.node.text')
                            ?: data_get($node, 'accessibility_caption')
                            ?: 'Postingan Instagram',
                        'likes' => (int) data_get($node, 'edge_liked_by.count', 0),
                        'comments' => (int) data_get($node, 'edge_media_to_comment.count', 0),
                        'is_video' => (bool) data_get($node, 'is_video', false),
                    ];
                })
                ->filter()
                ->take(12)
                ->values()
                ->all();

            return [
                'username' => (string) data_get($user, 'username', self::USERNAME),
                'full_name' => (string) data_get($user, 'full_name', 'KBC Crew Official'),
                'profile_url' => 'https://www.instagram.com/'.(string) data_get($user, 'username', self::USERNAME).'/',
                'profile_pic_url' => (string) data_get($user, 'profile_pic_url_hd', data_get($user, 'profile_pic_url', '')),
                'profile_pic_token' => $this->encodeMediaToken((string) data_get($user, 'profile_pic_url_hd', data_get($user, 'profile_pic_url', ''))),
                'bio' => (string) data_get($user, 'biography', ''),
                'followers' => (int) data_get($user, 'edge_followed_by.count', 0),
                'following' => (int) data_get($user, 'edge_follow.count', 0),
                'posts_count' => (int) data_get($user, 'edge_owner_to_timeline_media.count', 0),
                'items' => $items,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    private function encodeMediaToken(string $url): string
    {
        return rtrim(strtr(base64_encode($url), '+/', '-_'), '=');
    }

    private function decodeMediaToken(string $token): ?string
    {
        $base64 = strtr($token, '-_', '+/');
        $padding = strlen($base64) % 4;
        if ($padding > 0) {
            $base64 .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($base64, true);

        if (! is_string($decoded) || $decoded === '') {
            return null;
        }

        return $decoded;
    }

    private function isAllowedMediaHost(string $url): bool
    {
        $parts = parse_url($url);
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if ($scheme !== 'https' || $host === '') {
            return false;
        }

        return str_ends_with($host, '.fbcdn.net')
            || str_ends_with($host, '.cdninstagram.com')
            || str_ends_with($host, '.instagram.com');
    }
}
