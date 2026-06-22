<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('visitiranian.admin_email');

        $admin = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'مدیر سیستم',
                'phone' => '09120000000',
                'password' => Hash::make(config('visitiranian.admin_password')),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $admin->assignRole('admin');
    }
}
