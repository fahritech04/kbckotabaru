<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    public function store(?UploadedFile $file, string $directory, ?string $oldPath = null): ?string
    {
        if ($file === null) {
            return $oldPath;
        }

        if ($oldPath !== null && ! Str::startsWith($oldPath, ['http://', 'https://'])) {
            Storage::disk('public')->delete($oldPath);
        }

        return $file->store($directory, 'public');
    }

    public function resolveUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::url($path);
    }
}
