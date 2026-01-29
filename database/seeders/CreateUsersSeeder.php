<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateUsersSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'alumno']);

        $admin = User::firstOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Administrador',
            'password' => Hash::make('qwerty-1234'),
        ]);
        $admin->assignRole('admin');

        $alumno = User::firstOrCreate([
            'email' => 'alumno@example.com',
        ], [
            'name' => 'Alumno',
            'password' => Hash::make('qwerty-1234'),
        ]);
        $alumno->assignRole('alumno');
    }
}
