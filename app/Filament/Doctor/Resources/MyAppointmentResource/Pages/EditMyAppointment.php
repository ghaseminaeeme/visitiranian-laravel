<?php

namespace App\Filament\Doctor\Resources\MyAppointmentResource\Pages;

use App\Filament\Doctor\Resources\MyAppointmentResource;
use Filament\Resources\Pages\EditRecord;

class EditMyAppointment extends EditRecord
{
    protected static string $resource = MyAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) === 'cancelled' && empty($data['cancelled_at'])) {
            $data['cancelled_at'] = now();
        }

        if (($data['status'] ?? null) !== 'cancelled') {
            $data['cancellation_reason'] = null;
            $data['cancelled_at'] = null;
        }

        return $data;
    }
}
