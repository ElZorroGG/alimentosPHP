<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'productos.view',
            'productos.create',
            'productos.update',
            'productos.delete',
            'platos.view',
            'platos.create',
            'platos.update',
            'platos.delete',
            'menus.view',
            'menus.create',
            'menus.update',
            'menus.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $alumnoRole = Role::firstOrCreate(['name' => 'alumno']);

        $adminRole->syncPermissions(Permission::all());

        $alumnoPermissions = Permission::all()->filter(function ($p) {
            return $p->name !== 'productos.delete';
        });
        $alumnoRole->syncPermissions($alumnoPermissions);
    }
}
