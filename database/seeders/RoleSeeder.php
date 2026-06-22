<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'doctors.view',
            'doctors.create',
            'doctors.update',
            'doctors.delete',
            'doctors.publish',
            'appointments.view',
            'appointments.manage',
            'reviews.moderate',
            'pages.manage',
            'settings.manage',
            'users.manage',
            'ads.manage',
            'support.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::query()->firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions);

        $doctorRole = Role::query()->firstOrCreate(['name' => 'doctor']);
        $doctorRole->syncPermissions([
            'doctors.view',
            'doctors.update',
            'appointments.view',
            'appointments.manage',
        ]);
    }
}
