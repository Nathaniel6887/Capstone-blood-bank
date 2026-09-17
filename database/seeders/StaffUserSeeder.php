<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'Alexander Salazar, RMT',
                'email' => 'asalazar@crh.gov.ph',
                'role' => 'Registered Medical Technologist (RMT)',
                'employee_id' => 'CRH-EMP-002',
                'department' => 'Blood Bank & Transfusion Medicine',
                'phone_number' => '0917-445-9012',
                'status' => 'Active',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Kristine Lopez, RMT',
                'email' => 'klopez@crh.gov.ph',
                'role' => 'Registered Medical Technologist (RMT)',
                'employee_id' => 'CRH-EMP-003',
                'department' => 'Hematology & Immunohematology Section',
                'phone_number' => '0928-112-4456',
                'status' => 'Active',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Michael Cruz, RMT',
                'email' => 'mcruz@crh.gov.ph',
                'role' => 'Blood Bank Officer',
                'employee_id' => 'CRH-EMP-004',
                'department' => 'Blood Bank & Transfusion Medicine',
                'phone_number' => '0908-776-3321',
                'status' => 'Active',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Victoria Fernandez, MD, FPSP',
                'email' => 'vfernandez@crh.gov.ph',
                'role' => 'Laboratory Supervisor',
                'employee_id' => 'CRH-EMP-005',
                'department' => 'Laboratory & Clinical Pathology',
                'phone_number' => '0995-889-1100',
                'status' => 'Active',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Grace Beltran, RN',
                'email' => 'gbeltran@crh.gov.ph',
                'role' => 'Phlebotomist / Donor Care',
                'employee_id' => 'CRH-EMP-006',
                'department' => 'Donor Recruitment & Mobile Collection',
                'phone_number' => '0919-665-2289',
                'status' => 'On Leave',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Eduardo Navarro, RMT',
                'email' => 'enavarro@crh.gov.ph',
                'role' => 'Quality Assurance Officer',
                'employee_id' => 'CRH-EMP-007',
                'department' => 'Quality Assurance & Component Processing',
                'phone_number' => '0947-332-9988',
                'status' => 'Active',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($staffMembers as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                $staff
            );
        }
    }
}
