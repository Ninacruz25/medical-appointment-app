<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir roles
        $roles = [
            ['name' => 'Administrador', 'is_system' => true],
            ['name' => 'Super Administrador', 'is_system' => true],
            ['name' => 'Paciente', 'is_system' => true],
            ['name' => 'Doctor', 'is_system' => true],
            ['name' => 'Recepcionista', 'is_system' => true],
        ];
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
