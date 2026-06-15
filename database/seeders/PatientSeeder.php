<?php

namespace Database\Seeders;

use App\Models\BloodType;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            ['name' => 'Isabel Ruiz',       'email' => 'isabel@patient.com',   'id_number' => '70000020'],
            ['name' => 'Óscar Godoy',       'email' => 'oscar@patient.com',    'id_number' => '70000011'],
            ['name' => 'Berta Mota',        'email' => 'berta@patient.com',    'id_number' => '70000035'],
            ['name' => 'Elena Martínez',    'email' => 'elena@patient.com',    'id_number' => '70000042'],
            ['name' => 'Juan Rodríguez',    'email' => 'juan@patient.com',     'id_number' => '70000058'],
            ['name' => 'Carmen López',      'email' => 'carmen@patient.com',   'id_number' => '70000063'],
            ['name' => 'Miguel Flores',     'email' => 'miguel@patient.com',   'id_number' => '70000071'],
            ['name' => 'Laura Sánchez',     'email' => 'laura@patient.com',    'id_number' => '70000089'],
            ['name' => 'Pedro Morales',     'email' => 'pedro@patient.com',    'id_number' => '70000097'],
            ['name' => 'Ana Jiménez',       'email' => 'ana.p@patient.com',    'id_number' => '70000104'],
        ];

        $bloodTypes = BloodType::all()->keyBy('name');

        foreach ($patients as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'id_number'         => $data['id_number'],
                    'phone'             => '555' . rand(1000000, 9999999),
                    'address'           => 'Calle ' . rand(1, 100) . ' Col. Centro',
                    'email_verified_at' => now(),
                ]
            );

            Patient::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'blood_type_id'  => $bloodTypes->values()->get($i % $bloodTypes->count())?->id,
                    'allergies'      => $i % 3 === 0 ? 'Penicilina' : null,
                    'chronic_conditions' => $i % 4 === 0 ? 'Hipertensión arterial' : null,
                ]
            );
        }
    }
}
