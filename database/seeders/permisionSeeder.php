<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class permisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua route yang memiliki middleware "permission:"
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return collect($route->middleware())->contains(fn($middleware) => str_starts_with($middleware, 'permission:'))
                && $route->getName();
        })->map(function ($route) {
            return collect($route->middleware())->first(fn($middleware) => str_starts_with($middleware, 'permission:'));
        })->map(function ($middleware) {
            return str_replace('permission:', '', $middleware);
        })->unique();

        // **Reset semua permission dan role sebelum memasukkan data baru**
        Permission::query()->delete();
        Role::query()->delete();

        // Buat ulang permissions berdasarkan middleware
        $permissions = [];
        foreach ($routes as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            $permissions[] = $permission->id;
        }

        $this->command->info('Permissions successfully reset and seeded from middleware routes!');

        // **Reset role 'Super Admin'**
        $superAdminRole = Role::updateOrCreate(['name' => 'Super Admin']);

        // **Sync ulang permissions ke Super Admin**
        $superAdminRole->syncPermissions($permissions);

        // **Ambil user Super Admin, jika ada**
        $superAdmin = User::where('email', 'superadmin@tnks.com')->first();

        if ($superAdmin) {
            $superAdmin->syncRoles([$superAdminRole->name]);
            $this->command->info('Super Admin role assigned successfully!');
        } else {
            $this->command->warn('Super Admin user not found!');
        }
    }
}
