<?php

namespace Database\Seeders;

use App\Models\BloodIssue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BloodIssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $issues = [
            [
                'requisition_no' => 'REQ-2026-0412',
                'patient_name' => 'Elena Roxas',
                'patient_hrn' => 'HRN-99821',
                'ward_room' => 'ICU - Intensive Care Unit',
                'blood_type' => 'O+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 2,
                'attending_physician' => 'Dr. G. Morales',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Urgent',
                'status' => 'Transfusion Ongoing',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Post-laparotomy hemorrhage monitoring. Check vitals q15m.',
                'issued_at' => Carbon::now()->subHours(2),
            ],
            [
                'requisition_no' => 'REQ-2026-0413',
                'patient_name' => 'Roberto Tan',
                'patient_hrn' => 'HRN-99834',
                'ward_room' => 'Emergency Trauma & Acute Care',
                'blood_type' => 'A+',
                'blood_component' => 'Fresh Frozen Plasma (FFP)',
                'units_issued' => 1,
                'attending_physician' => 'Dr. V. Fernandez',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Stat/Emergency',
                'status' => 'Completed',
                'issued_by' => 'MedTech K. Lopez, RMT',
                'remarks' => 'Coagulopathy correction in motor vehicle trauma victim.',
                'issued_at' => Carbon::now()->subHours(5),
            ],
            [
                'requisition_no' => 'REQ-2026-0414',
                'patient_name' => 'Cynthia Gomez',
                'patient_hrn' => 'HRN-99850',
                'ward_room' => 'OB-GYN & Delivery Ward',
                'blood_type' => 'B+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 2,
                'attending_physician' => 'Dr. C. Alcantara',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Stat/Emergency',
                'status' => 'Completed',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Postpartum hemorrhage emergency transfusion.',
                'issued_at' => Carbon::now()->subHours(8),
            ],
            [
                'requisition_no' => 'REQ-2026-0415',
                'patient_name' => 'Danilo Castro',
                'patient_hrn' => 'HRN-99877',
                'ward_room' => 'Hemodialysis Center',
                'blood_type' => 'AB+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 1,
                'attending_physician' => 'Dr. R. Navarro',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Issued',
                'issued_by' => 'MedTech M. Cruz, RMT',
                'remarks' => 'End-stage renal disease severe symptomatic anemia (Hb 6.8 g/dL).',
                'issued_at' => Carbon::now()->subMinutes(45),
            ],
            [
                'requisition_no' => 'REQ-2026-0416',
                'patient_name' => 'Marcus Aurelio Villanueva',
                'patient_hrn' => 'HRN-99892',
                'ward_room' => 'Operating Room / PACU',
                'blood_type' => 'O-',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 2,
                'attending_physician' => 'Dr. G. Morales',
                'crossmatch_status' => 'Emergency Uncrossmatched',
                'urgency_level' => 'Stat/Emergency',
                'status' => 'Transfusion Ongoing',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Emergency exploratory laparotomy with vascular rupture.',
                'issued_at' => Carbon::now()->subMinutes(90),
            ],
            [
                'requisition_no' => 'REQ-2026-0417',
                'patient_name' => 'Angelica Mae Cruz',
                'patient_hrn' => 'HRN-99905',
                'ward_room' => 'Pediatrics & NICU',
                'blood_type' => 'A-',
                'blood_component' => 'Platelet Concentrate (PC)',
                'units_issued' => 4,
                'attending_physician' => 'Dr. M. Santos',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Urgent',
                'status' => 'Issued',
                'issued_by' => 'MedTech K. Lopez, RMT',
                'remarks' => 'Dengue hemorrhagic fever thrombocytopenia (Plt 18,000/uL).',
                'issued_at' => Carbon::now()->subHours(1),
            ],
            [
                'requisition_no' => 'REQ-2026-0418',
                'patient_name' => 'Bernardo Silvestre',
                'patient_hrn' => 'HRN-99918',
                'ward_room' => 'Surgical Inpatient Ward',
                'blood_type' => 'B-',
                'blood_component' => 'Whole Blood (WB)',
                'units_issued' => 1,
                'attending_physician' => 'Dr. V. Fernandez',
                'crossmatch_status' => 'Pending Crossmatch',
                'urgency_level' => 'Routine',
                'status' => 'Issued',
                'issued_by' => 'MedTech M. Cruz, RMT',
                'remarks' => 'Pre-operative reservation for elective total hip replacement.',
                'issued_at' => Carbon::now()->subMinutes(20),
            ],
        ];

        foreach ($issues as $issue) {
            BloodIssue::updateOrCreate(
                ['requisition_no' => $issue['requisition_no']],
                $issue
            );
        }
    }
}
