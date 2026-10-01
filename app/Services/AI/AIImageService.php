<?php

namespace App\Services\AI;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AIImageService
{
    public function store(UploadedFile $image): string
    {
        return $image->store('chat_ai', 'public');
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }

    public function publicUrl(?string $path): ?string
    {
        return $path ? Storage::url($path) : null;
    }
}
