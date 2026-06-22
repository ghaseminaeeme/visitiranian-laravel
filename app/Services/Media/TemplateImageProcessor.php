<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\DisplayTemplate;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;

final class TemplateImageProcessor
{
    /**
     * @return array{path: string, width: int, height: int, mime: string}
     */
    public function process(
        string $sourcePath,
        DisplayTemplate $template,
        ?string $destinationDirectory = null,
    ): array {
        if (! is_file($sourcePath)) {
            throw new RuntimeException('Source image not found: '.$sourcePath);
        }

        $width = (int) $template->image_width;
        $height = (int) $template->image_height;

        if ($width <= 0 || $height <= 0) {
            throw new RuntimeException('Display template dimensions are invalid');
        }

        $image = Image::read($sourcePath);
        $image->cover($width, $height);

        $destinationDirectory ??= 'processed/'.($template->key ?? 'default');
        $filename = pathinfo($sourcePath, PATHINFO_FILENAME).'.webp';
        $relativePath = trim($destinationDirectory, '/').'/'.$filename;

        $encoded = $image->toWebp(quality: 85);
        Storage::disk('public')->put($relativePath, (string) $encoded);

        return [
            'path' => $relativePath,
            'width' => $width,
            'height' => $height,
            'mime' => 'image/webp',
        ];
    }

    /**
     * @return array{path: string, width: int, height: int, mime: string}
     */
    public function processFromUpload(string $uploadedPath, DisplayTemplate $template): array
    {
        $absolutePath = Storage::disk('local')->path($uploadedPath);

        return $this->process($absolutePath, $template);
    }
}
