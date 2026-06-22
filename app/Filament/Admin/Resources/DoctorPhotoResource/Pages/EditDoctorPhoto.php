<?php

namespace App\Filament\Admin\Resources\DoctorPhotoResource\Pages;

use App\Filament\Admin\Resources\DoctorPhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorPhoto extends EditRecord
{
    protected static string $resource = DoctorPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
