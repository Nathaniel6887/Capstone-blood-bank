<?php

namespace Database\Seeders;

use App\Models\User;
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
        $this->call(AdminUserSeeder::class);
        $this->call(CaragaMunicipalitiesSeeder::class);
        $this->call(BloodDonorSeeder::class);
        $this->call(BloodIssueSeeder::class);
        $this->call(StaffUserSeeder::class);
        $this->call(BloodReportSeeder::class);
    }
}
