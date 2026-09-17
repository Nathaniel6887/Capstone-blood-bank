<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'crhbloodbank@crh.gov.ph'],
            [
                'name' => 'BloodbankCRH',
                'role' => 'System Administrator',
                'employee_id' => 'CRH-ADMIN-01',
                'department' => 'Blood Bank & Transfusion Medicine',
                'phone_number' => '(086) 826-2459',
                'status' => 'Active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
    }
}
