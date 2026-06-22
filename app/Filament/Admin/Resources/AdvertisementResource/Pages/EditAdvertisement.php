<?php

namespace App\Filament\Admin\Resources\AdvertisementResource\Pages;

use App\Filament\Admin\Resources\AdvertisementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdvertisement extends EditRecord
{
    protected static string $resource = AdvertisementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
