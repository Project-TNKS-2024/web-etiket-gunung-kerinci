<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmergencyPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'manage-emergency',
            'view-tracking',
            'respond-sos',
            'manage-posts',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Assign all to Super Admin
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo($permissions);

        // Admin Destinasi gets view-tracking and respond-sos
        $adminDest = Role::firstOrCreate(['name' => 'Admin Destinasi']);
        $adminDest->givePermissionTo(['view-tracking', 'respond-sos', 'manage-emergency']);

        $this->command->info('Emergency/Tracking/SOS permissions seeded.');
    }
}
