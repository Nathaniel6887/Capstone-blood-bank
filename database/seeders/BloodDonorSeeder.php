<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use Illuminate\Database\Seeder;

class BloodDonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donors = [
            [
                'full_name' => 'Maria Elena Santos',
                'blood_type' => 'O+',
                'age' => 28,
                'gender' => 'Female',
                'barangay' => 'Poblacion 1',
                'municipality' => 'Buenavista',
                'contact_information' => '0917-882-9012',
                'total_donations' => 4,
            ],
            [
                'full_name' => 'Juan Carlo Dela Cruz',
                'blood_type' => 'A+',
                'age' => 34,
                'gender' => 'Male',
                'barangay' => 'San Pedro',
                'municipality' => 'San Jose',
                'contact_information' => '0928-341-7782',
                'total_donations' => 7,
            ],
            [
                'full_name' => 'Roberto V. Mendoza',
                'blood_type' => 'B+',
                'age' => 41,
                'gender' => 'Male',
                'barangay' => 'Santa Irene',
                'municipality' => 'Prosperidad',
                'contact_information' => '0908-554-1290',
                'total_donations' => 12,
            ],
            [
                'full_name' => 'Ana Patricia Ramos',
                'blood_type' => 'AB+',
                'age' => 25,
                'gender' => 'Female',
                'barangay' => 'Catangnan',
                'municipality' => 'General Luna',
                'contact_information' => '0995-123-8890',
                'total_donations' => 2,
            ],
            [
                'full_name' => 'Gabriel R. Fernandez',
                'blood_type' => 'O-',
                'age' => 39,
                'gender' => 'Male',
                'barangay' => 'Linintian',
                'municipality' => 'Cantilan',
                'contact_information' => '0947-890-3341',
                'total_donations' => 9,
            ],
            [
                'full_name' => 'Kristine Joy Alcantara',
                'blood_type' => 'A+',
                'age' => 31,
                'gender' => 'Female',
                'barangay' => 'Triangulo',
                'municipality' => 'Nasipit',
                'contact_information' => '0919-456-2211',
                'total_donations' => 5,
            ],
        ];

        foreach ($donors as $donor) {
            BloodDonor::updateOrCreate(
                [
                    'full_name' => $donor['full_name'],
                    'contact_information' => $donor['contact_information'],
                ],
                $donor
            );
        }
    }
}
