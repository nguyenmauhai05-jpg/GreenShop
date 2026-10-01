<?php

namespace App\Services\Plants;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class PlantImageService
{
    private const DIRECTORY = 'images/cay-canh';

    public function store(UploadedFile $file): string
    {
        $directory = public_path(self::DIRECTORY);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $fileName = time()
            . '_'
            . uniqid()
            . '.'
            . $file->getClientOriginalExtension();

        $file->move($directory, $fileName);

        return self::DIRECTORY . '/' . $fileName;
    }

    public function replace(UploadedFile $file, ?string $oldPath): string
    {
        $newPath = $this->store($file);
        $this->delete($oldPath);

        return $newPath;
    }

    public function delete(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $absolutePath = public_path($path);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
