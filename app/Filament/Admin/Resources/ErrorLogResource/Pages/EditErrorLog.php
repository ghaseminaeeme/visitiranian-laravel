<?php

namespace App\Filament\Admin\Resources\ErrorLogResource\Pages;

use App\Filament\Admin\Resources\ErrorLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditErrorLog extends EditRecord
{
    protected static string $resource = ErrorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
