<?php

namespace App\Filament\Admin\Resources\SpecialtyResource\Pages;

use App\Filament\Admin\Resources\SpecialtyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpecialties extends ListRecords
{
    protected static string $resource = SpecialtyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
