<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Roles y permisos
        $this->call(RolesAndPermissionsSeeder::class);

        // 2) Usuario admin
        $adminUser = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        // 3) Asignar rol Admin
        $adminRole = Role::where('name', 'Admin')->first();
        $adminUser->assignRole($adminRole);
    }
}
