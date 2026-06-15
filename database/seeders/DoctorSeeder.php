<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            ['name' => 'Dr. Carlos Pérez',       'specialty' => 'Cardiología',       'email' => 'carlos@test.com',    'id_number' => '30000001', 'license' => 'MED001'],
            ['name' => 'Dra. Ana Gómez',          'specialty' => 'Dermatología',      'email' => 'ana@test.com',       'id_number' => '30000002', 'license' => 'MED002'],
            ['name' => 'Dr. Luis Torres',         'specialty' => 'Neurología',        'email' => 'luis@test.com',      'id_number' => '30000003', 'license' => 'MED003'],
            ['name' => 'Dra. María Hernández',    'specialty' => 'Pediatría',         'email' => 'maria@test.com',     'id_number' => '30000004', 'license' => 'MED004'],
            ['name' => 'Dr. Jorge Ramírez',       'specialty' => 'Psiquiatría',       'email' => 'jorge@test.com',     'id_number' => '30000005', 'license' => 'MED005'],
            ['name' => 'Dra. Sofía Castillo',     'specialty' => 'Oncología',         'email' => 'sofia@test.com',     'id_number' => '30000006', 'license' => 'MED006'],
            ['name' => 'Dr. Roberto Mendoza',     'specialty' => 'Ortopedia',         'email' => 'roberto@test.com',   'id_number' => '30000007', 'license' => 'MED007'],
            ['name' => 'Dra. Patricia Vargas',    'specialty' => 'Gastroenterología', 'email' => 'patricia@test.com',  'id_number' => '30000008', 'license' => 'MED008'],
        ];

        foreach ($doctors as $data) {
            $specialty = Specialty::where('name', $data['specialty'])->first();

            if (!$specialty) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => Hash::make('password'),
                    'id_number' => $data['id_number'],
                    'phone'     => '333' . rand(1000000, 9999999),
                    'address'   => 'Consultorio ' . rand(1, 50),
                    'email_verified_at' => now(),
                ]
            );

            Doctor::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id'           => $specialty->id,
                    'medical_license_number' => $data['license'],
                    'biography'              => 'Especialista en ' . $data['specialty'] . ' con amplia experiencia clínica.',
                ]
            );
        }
    }
}
