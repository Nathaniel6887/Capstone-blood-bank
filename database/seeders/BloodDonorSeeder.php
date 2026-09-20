<?php

namespace Database\Seeders;

use App\Models\BloodDonor;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BloodDonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donors = [
            // Q1 2026 (January to March) - 4 Donors
            [
                'full_name' => 'Juan Dela Cruz',
                'blood_type' => 'O+',
                'age' => 34,
                'gender' => 'Male',
                'barangay' => 'Tubajon',
                'municipality' => 'Tubajon, Dinagat Islands',
                'contact_information' => '0918-765-4321',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-01-14 09:30:00'),
                'updated_at' => Carbon::parse('2026-01-14 09:30:00'),
            ],
            [
                'full_name' => 'Maria Santos',
                'blood_type' => 'A+',
                'age' => 23,
                'gender' => 'Female',
                'barangay' => 'San Jose',
                'municipality' => 'San Jose, Dinagat Islands',
                'contact_information' => '0917-123-4567',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-01-19 14:15:00'),
                'updated_at' => Carbon::parse('2026-01-19 14:15:00'),
            ],
            [
                'full_name' => 'Pedro Garcia',
                'blood_type' => 'B+',
                'age' => 42,
                'gender' => 'Male',
                'barangay' => 'Basilisa',
                'municipality' => 'Basilisa, Dinagat Islands',
                'contact_information' => '0920-555-0192',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-02-09 10:00:00'),
                'updated_at' => Carbon::parse('2026-02-09 10:00:00'),
            ],
            [
                'full_name' => 'Elena Rodriguez',
                'blood_type' => 'O+',
                'age' => 29,
                'gender' => 'Female',
                'barangay' => 'Cagdianao',
                'municipality' => 'Cagdianao, Dinagat Islands',
                'contact_information' => '0919-888-2233',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-03-12 11:20:00'),
                'updated_at' => Carbon::parse('2026-03-12 11:20:00'),
            ],

            // Q2 2026 (April to June) - 3 Donors
            [
                'full_name' => 'Roberto Mendoza',
                'blood_type' => 'B+',
                'age' => 38,
                'gender' => 'Male',
                'barangay' => 'Libjo',
                'municipality' => 'Libjo, Dinagat Islands',
                'contact_information' => '0908-554-1290',
                'total_donations' => 2,
                'created_at' => Carbon::parse('2026-04-18 13:45:00'),
                'updated_at' => Carbon::parse('2026-04-18 13:45:00'),
            ],
            [
                'full_name' => 'Kristine Alcantara',
                'blood_type' => 'A+',
                'age' => 31,
                'gender' => 'Female',
                'barangay' => 'Loreto',
                'municipality' => 'Loreto, Dinagat Islands',
                'contact_information' => '0919-456-2211',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-05-22 08:30:00'),
                'updated_at' => Carbon::parse('2026-05-22 08:30:00'),
            ],
            [
                'full_name' => 'Danilo Castro',
                'blood_type' => 'AB+',
                'age' => 45,
                'gender' => 'Male',
                'barangay' => 'San Jose',
                'municipality' => 'San Jose, Dinagat Islands',
                'contact_information' => '0912-345-6789',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-06-15 15:10:00'),
                'updated_at' => Carbon::parse('2026-06-15 15:10:00'),
            ],

            // Q3 2026 (July to September) - 4 Donors
            [
                'full_name' => 'Ana Patricia Ramos',
                'blood_type' => 'AB+',
                'age' => 26,
                'gender' => 'Female',
                'barangay' => 'Tubajon',
                'municipality' => 'Tubajon, Dinagat Islands',
                'contact_information' => '0995-123-8890',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-07-04 09:00:00'),
                'updated_at' => Carbon::parse('2026-07-04 09:00:00'),
            ],
            [
                'full_name' => 'Gabriel Fernandez',
                'blood_type' => 'O-',
                'age' => 39,
                'gender' => 'Male',
                'barangay' => 'Basilisa',
                'municipality' => 'Basilisa, Dinagat Islands',
                'contact_information' => '0947-890-3341',
                'total_donations' => 2,
                'created_at' => Carbon::parse('2026-08-11 16:20:00'),
                'updated_at' => Carbon::parse('2026-08-11 16:20:00'),
            ],
            [
                'full_name' => 'Carlos Miguel Tan',
                'blood_type' => 'A-',
                'age' => 33,
                'gender' => 'Male',
                'barangay' => 'Dinagat',
                'municipality' => 'Dinagat, Dinagat Islands',
                'contact_information' => '0917-654-3210',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-08-28 10:45:00'),
                'updated_at' => Carbon::parse('2026-08-28 10:45:00'),
            ],
            [
                'full_name' => 'Lourdes Reyes',
                'blood_type' => 'O+',
                'age' => 27,
                'gender' => 'Female',
                'barangay' => 'Cagdianao',
                'municipality' => 'Cagdianao, Dinagat Islands',
                'contact_information' => '0928-111-4455',
                'total_donations' => 1,
                'created_at' => Carbon::parse('2026-09-08 14:00:00'),
                'updated_at' => Carbon::parse('2026-09-08 14:00:00'),
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
