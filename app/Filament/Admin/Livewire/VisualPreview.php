<?php

namespace App\Filament\Admin\Livewire;

use Livewire\Component;

class VisualPreview extends Component
{
    public ?string $title = null;

    public ?string $subtitle = null;

    public ?string $ctaText = null;

    public ?string $ctaUrl = null;

    public ?string $imageUrl = null;

    public ?int $imageWidth = null;

    public ?int $imageHeight = null;

    public function render()
    {
        return view('filament.admin.livewire.visual-preview');
    }
}
