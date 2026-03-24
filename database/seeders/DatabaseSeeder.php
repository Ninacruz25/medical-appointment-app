<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Lammar a los seeder creados
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);

        // Crear usuario de prueba
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@test.com',
        //     'password' => bcrypt('password'),
        // ]);
    }
}
