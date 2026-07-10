<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;

final class ImageUpload
{
    public static function make(string $name, string $directory, ?string $label = 'تصویر'): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->imagePreviewHeight('120')
            ->panelLayout('compact')
            ->panelAspectRatio('16:9')
            ->maxSize(5120)
            ->openable()
            ->downloadable()
            ->extraAttributes(['class' => 'vi-image-upload'])
            ->helperText('برای انتخاب تصویر، روی کادر زیر ضربه بزنید');
    }

    public static function avatar(string $name, string $directory, ?string $label = 'تصویر پروفایل'): FileUpload
    {
        return static::make($name, $directory, $label)
            ->avatar()
            ->imagePreviewHeight('96')
            ->panelAspectRatio('1:1')
            ->extraAttributes(['class' => 'vi-image-upload vi-image-upload--avatar']);
    }
}
