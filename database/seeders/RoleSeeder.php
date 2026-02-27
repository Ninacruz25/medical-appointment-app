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
            'paciente',
            'Doctor',
            'admin',
            'Super Admin',
        ];
        foreach ($roles as $role) {
            Role::create([
                'name' => $role
            ]);
        }
    }
}
