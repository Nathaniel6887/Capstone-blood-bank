<?php

namespace Database\Seeders;

use App\Models\BloodReport;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BloodReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = [
            // Inside Caraga Reports
            [
                'report_code' => 'RPT-CRH-2026-001',
                'scope' => 'Inside Caraga',
                'facility_name' => 'Surigao City Health Office & Blood Station',
                'province_region' => 'Surigao del Norte (Caraga)',
                'report_type' => 'Monthly Blood Collection Turnout',
                'reporting_period' => 'September 2026',
                'blood_units_count' => 45,
                'blood_breakdown' => 'O+: 18, A+: 14, B+: 9, AB+: 3, O-: 1',
                'submitting_officer' => 'Dr. Emmanuel Salcedo, MD - City Health Officer',
                'status' => 'Verified',
                'summary_notes' => 'Regular voluntary mobile blood drive turnout in Barangay Washington, San Juan, and Luna.',
                'submitted_at' => Carbon::now()->subDays(2),
            ],
            [
                'report_code' => 'RPT-CRH-2026-002',
                'scope' => 'Inside Caraga',
                'facility_name' => 'Butuan Medical Center (BMC) Blood Bank Section',
                'province_region' => 'Agusan del Norte (Caraga)',
                'report_type' => 'Inter-Regional Blood Transfer & Requisition',
                'reporting_period' => 'September 2026',
                'blood_units_count' => 20,
                'blood_breakdown' => 'O+: 10, A+: 5, B+: 3, AB+: 2',
                'submitting_officer' => 'MedTech Joanna Rivera, RMT - Blood Bank Officer',
                'status' => 'Verified',
                'summary_notes' => 'Mutual assistance buffer stock transfer to Caraga Regional Hospital for scheduled cardiothoracic cases.',
                'submitted_at' => Carbon::now()->subDays(4),
            ],
            [
                'report_code' => 'RPT-CRH-2026-003',
                'scope' => 'Inside Caraga',
                'facility_name' => 'Siargao Island District Hospital - Dapa',
                'province_region' => 'Surigao del Norte (Caraga)',
                'report_type' => 'Mobile Blood Drive Turnout Audit',
                'reporting_period' => 'August - September 2026',
                'blood_units_count' => 28,
                'blood_breakdown' => 'O+: 12, A+: 8, B+: 6, AB+: 1, O-: 1',
                'submitting_officer' => 'Dr. Ronald Alcantara, MD - Chief of Hospital',
                'status' => 'Verified',
                'summary_notes' => 'General Luna, Del Carmen, and Dapa coastal community blood donation campaign turnover.',
                'submitted_at' => Carbon::now()->subDays(6),
            ],
            [
                'report_code' => 'RPT-CRH-2026-004',
                'scope' => 'Inside Caraga',
                'facility_name' => 'Dinagat Islands Provincial Blood Center - San Jose',
                'province_region' => 'Dinagat Islands (Caraga)',
                'report_type' => 'Monthly Blood Collection Turnout',
                'reporting_period' => 'September 2026',
                'blood_units_count' => 16,
                'blood_breakdown' => 'O+: 8, A+: 4, B+: 3, AB+: 1',
                'submitting_officer' => 'MedTech Cheryl Evasco, RMT',
                'status' => 'Received',
                'summary_notes' => 'Provincial health board island collection summary and cold-chain sea transport compliance log.',
                'submitted_at' => Carbon::now()->subHours(12),
            ],

            // Outside Caraga Reports
            [
                'report_code' => 'RPT-EXT-2026-101',
                'scope' => 'Outside Caraga',
                'facility_name' => 'Southern Philippines Medical Center (SPMC) Blood Bank - Davao',
                'province_region' => 'Region XI - Davao Region (SPMC / Davao Blood Center)',
                'report_type' => 'Inter-Regional Blood Transfer & Requisition',
                'reporting_period' => 'September 2026',
                'blood_units_count' => 30,
                'blood_breakdown' => 'Packed RBC O+: 15, FFP A+: 10, Cryoprecipitate: 5',
                'submitting_officer' => 'Dr. Fernando Tan, MD, FPSP - SPMC Transfusion Head',
                'status' => 'Verified',
                'summary_notes' => 'Emergency inter-regional component allocation for major trauma and dengue outbreak preparedness in Eastern Mindanao.',
                'submitted_at' => Carbon::now()->subDays(3),
            ],
            [
                'report_code' => 'RPT-EXT-2026-102',
                'scope' => 'Outside Caraga',
                'facility_name' => 'Northern Mindanao Medical Center (NMMC) - Cagayan de Oro',
                'province_region' => 'Region X - Northern Mindanao (CDO / NMMC)',
                'report_type' => 'Inter-Regional Blood Transfer & Requisition',
                'reporting_period' => 'August 2026',
                'blood_units_count' => 25,
                'blood_breakdown' => 'PRBC O-: 4, PRBC A-: 6, PRBC B-: 5, Whole Blood: 10',
                'submitting_officer' => 'MedTech Roberto Gomez, RMT - Regional Blood Coordinator',
                'status' => 'Verified',
                'summary_notes' => 'Rare Rh-negative inventory exchange and emergency cross-matching referral verification report.',
                'submitted_at' => Carbon::now()->subDays(8),
            ],
            [
                'report_code' => 'RPT-EXT-2026-103',
                'scope' => 'Outside Caraga',
                'facility_name' => 'Philippine Red Cross National Blood Services - Manila HQ',
                'province_region' => 'National Capital Region (NCR - Manila DOH / PRC)',
                'report_type' => 'Emergency Disaster Blood Allocation',
                'reporting_period' => 'Q3 2026',
                'blood_units_count' => 50,
                'blood_breakdown' => 'Platelet Concentrate: 25, FFP: 15, Whole Blood: 10',
                'submitting_officer' => 'Dr. Maricar Santos, MD - PRC National Blood Director',
                'status' => 'Received',
                'summary_notes' => 'National strategic buffer replenishment for Mindanao tertiary regional referral hospitals via air freight.',
                'submitted_at' => Carbon::now()->subHours(18),
            ],
            [
                'report_code' => 'RPT-EXT-2026-104',
                'scope' => 'Outside Caraga',
                'facility_name' => 'Vicente Sotto Memorial Medical Center (VSMMC) - Cebu City',
                'province_region' => 'Region VII - Central Visayas (Cebu / VSMMC)',
                'report_type' => 'Serological Safety & Screening Summary',
                'reporting_period' => 'Q3 2026 Audit',
                'blood_units_count' => 12,
                'blood_breakdown' => 'Apheresis Platelets: 12 Units',
                'submitting_officer' => 'Dr. Kenneth Yap, MD, FPSP - Immunohematology Lead',
                'status' => 'Pending Review',
                'summary_notes' => 'Specialized HLA-matched apheresis platelets screening report for severe pediatric hematology recipient.',
                'submitted_at' => Carbon::now()->subHours(5),
            ],
        ];

        foreach ($reports as $report) {
            BloodReport::updateOrCreate(
                ['report_code' => $report['report_code']],
                $report
            );
        }
    }
}
