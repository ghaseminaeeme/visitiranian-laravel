<?php

namespace App\Filament\Admin\Resources\DoctorResource\Pages;

use App\Filament\Admin\Resources\DoctorResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset(
            $data['create_user'],
            $data['user_name'],
            $data['user_email'],
            $data['user_phone'],
            $data['user_password'],
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $state = $this->form->getState();

        if (! ($state['create_user'] ?? false)) {
            return;
        }

        $user = User::query()->create([
            'name' => $state['user_name'] ?? $this->record->name,
            'email' => $state['user_email'],
            'phone' => $state['user_phone'] ?? null,
            'password' => Hash::make($state['user_password'] ?? 'password'),
            'is_active' => true,
        ]);

        $user->assignRole('doctor');

        $this->record->update(['user_id' => $user->id]);
    }
}
