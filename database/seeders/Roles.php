<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleUsuario = Role::create(['name' => 'usuario']);

        $permissionCrearProducto = Permission::create(['name' => 'crear_producto']);
        $permissionEditarProducto = Permission::create(['name' => 'editar_producto']);
        $permissionEliminarProducto = Permission::create(['name' => 'eliminar_producto']);

        $roleAdmin->givePermissionTo($permissionCrearProducto);
        $roleAdmin->givePermissionTo($permissionEditarProducto);
        $roleAdmin->givePermissionTo($permissionEliminarProducto);
        
        $roleUsuario->givePermissionTo($permissionCrearProducto);
        $roleUsuario->givePermissionTo($permissionEditarProducto);

        $user = User::find(2);
        if ($user) {
            $user->assignRole('admin');
        }
        $user = User::find(1);
        if ($user) {
            $user->assignRole('usuario');
        }
    }
}