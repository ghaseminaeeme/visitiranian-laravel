<?php

namespace App\Filament\Admin\Resources\SupportTicketResource\Pages;

use App\Filament\Admin\Resources\SupportTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['ticket_number'])) {
            $data['ticket_number'] = 'TKT-'.strtoupper(Str::random(8));
        }

        return $data;
    }
}
