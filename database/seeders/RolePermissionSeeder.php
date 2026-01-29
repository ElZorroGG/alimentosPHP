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

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user = Role::firstOrCreate(['name' => 'user']);

        $admin->givePermissionTo(Permission::all());

        $userPerms = Permission::all()->filter(function ($p) {
            return $p->name !== 'productos.delete';
        });
        $user->syncPermissions($userPerms->pluck('name')->all());
    }
}
