<?php

namespace App\Filament\Admin\Resources\SmsTemplateResource\Pages;

use App\Filament\Admin\Resources\SmsTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmsTemplate extends EditRecord
{
    protected static string $resource = SmsTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
