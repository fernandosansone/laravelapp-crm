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

        // Permisos por recurso/acción
        $resources = ['contacts', 'opportunities', 'followups', 'quotes', 'reports', 'users', 'roles', 'permissions'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($resources as $res) {
            foreach ($actions as $act) {
                Permission::firstOrCreate(['name' => "{$res}.{$act}", 'guard_name' => 'web']);
            }
        }

        // Extras
        Permission::firstOrCreate(['name' => 'dashboard.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'agenda.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'agenda.followups.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'opportunities.change_status', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'opportunities.view_all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'opportunities.update_all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'opportunities.delete_all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'opportunities.change_status_all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'followups.view_all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'followups.create_all', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'reports.export', 'guard_name' => 'web']);

        // Role
        $admin = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor', 'guard_name' => 'web']);
        $exec = Role::firstOrCreate(['name' => 'Ejecutivo', 'guard_name' => 'web']);

        // Admin: todo
        $admin->syncPermissions(Permission::all());

        // Supervisor: todo menos borrar usuarios (ejemplo)
        $supervisor->syncPermissions(
            Permission::whereNotIn('name', ['users.delete', 'roles.delete', 'permissions.delete'])->get()
        );

        // Ejecutivo: CRM básico (ajustable)
        $exec->syncPermissions(
            Permission::whereIn('name', [
                'contacts.view','contacts.create','contacts.update',
                'opportunities.view','opportunities.create','opportunities.update','opportunities.change_status',
                'followups.view','followups.create','followups.update',
                'quotes.view','quotes.create','quotes.update',
                'reports.view',
                'dashboard.view',
                'agenda.view', 'agenda.followups.create',
                'users.view', 
            ])->get()
        );
    }
}
