<?php

namespace App\Filament\Doctor\Resources\MyAppointmentResource\Pages;

use App\Filament\Doctor\Resources\MyAppointmentResource;
use Filament\Resources\Pages\ListRecords;

class ListMyAppointments extends ListRecords
{
    protected static string $resource = MyAppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
