<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos por recurso/acción (base para CRM)
        $resources = ['contacts', 'opportunities', 'followups', 'quotes', 'reports', 'users'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($resources as $res) {
            foreach ($actions as $act) {
                Permission::firstOrCreate(['name' => "{$res}.{$act}"]);
            }
        }

        // Extras típicos
        Permission::firstOrCreate(['name' => 'reports.export']);
        Permission::firstOrCreate(['name' => 'opportunities.change_status']);

        // Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor']);
        $exec = Role::firstOrCreate(['name' => 'Ejecutivo']);

        // Admin: todo
        $admin->syncPermissions(Permission::all());

        // Supervisor: todo menos borrar usuarios (ejemplo)
        $supervisor->syncPermissions(
            Permission::whereNotIn('name', ['users.delete'])->get()
        );

        // Ejecutivo: CRM básico (ajustable)
        $exec->syncPermissions(
            Permission::whereIn('name', [
                'contacts.view','contacts.create','contacts.update',
                'opportunities.view','opportunities.create','opportunities.update','opportunities.change_status',
                'followups.view','followups.create','followups.update',
                'quotes.view','quotes.create','quotes.update',
                'reports.view',
            ])->get()
        );
    }
}
