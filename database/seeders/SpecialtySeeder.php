<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            'Cardiología',
            'Dermatología',
            'Neurología',
            'Pediatría',
            'Psiquiatría',
            'Oncología',
            'Ortopedia',
            'Gastroenterología',
        ];

        foreach ($specialties as $specialty) {
            \App\Models\Specialty::create(['name' => $specialty]);
        }
    }
}
