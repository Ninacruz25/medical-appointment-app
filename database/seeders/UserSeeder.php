<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'id_number' => '123456789',
            'phone' => '9999999999',
            'address' => 'Test address',

        ])->assignRole('Administrador');
    }
}
