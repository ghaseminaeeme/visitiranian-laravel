<?php

namespace App\Filament\Admin\Resources\AppointmentResource\Pages;

use App\Filament\Admin\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['tracking_code'])) {
            $data['tracking_code'] = strtoupper(Str::random(8));
        }

        if (empty($data['booked_at'])) {
            $data['booked_at'] = now();
        }

        return $data;
    }
}
