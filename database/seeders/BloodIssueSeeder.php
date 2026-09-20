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
                'requisition_no' => 'REQ-2026-0001',
                'patient_name' => 'Elena Roxas',
                'patient_hrn' => 'HRN-99821',
                'ward_room' => 'Tubajon',
                'blood_type' => 'O+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 8,
                'attending_physician' => 'Dr. G. Morales',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Issued',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Municipal health blood stock replenishment and maternal care reserve.',
                'issued_at' => Carbon::parse('2026-01-30 09:30:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0002',
                'patient_name' => 'Roberto Tan',
                'patient_hrn' => 'HRN-99834',
                'ward_room' => 'Tubajon',
                'blood_type' => 'O+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 12,
                'attending_physician' => 'Dr. V. Fernandez',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Urgent',
                'status' => 'Completed',
                'issued_by' => 'MedTech K. Lopez, RMT',
                'remarks' => 'Emergency emergency transfusions and rural trauma center support.',
                'issued_at' => Carbon::parse('2026-02-27 11:15:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0003',
                'patient_name' => 'Cynthia Gomez',
                'patient_hrn' => 'HRN-99850',
                'ward_room' => 'Tubajon',
                'blood_type' => 'A+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 5,
                'attending_physician' => 'Dr. C. Alcantara',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Completed',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Surgical allocation and obstetrics standby units.',
                'issued_at' => Carbon::parse('2026-03-30 14:00:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0004',
                'patient_name' => 'Danilo Castro',
                'patient_hrn' => 'HRN-99877',
                'ward_room' => 'San Jose',
                'blood_type' => 'B+',
                'blood_component' => 'Whole Blood (WB)',
                'units_issued' => 7,
                'attending_physician' => 'Dr. R. Navarro',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Issued',
                'issued_by' => 'MedTech M. Cruz, RMT',
                'remarks' => 'San Jose District Health Infirmary scheduled supply.',
                'issued_at' => Carbon::parse('2026-04-29 10:20:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0005',
                'patient_name' => 'Marcus Aurelio Villanueva',
                'patient_hrn' => 'HRN-99892',
                'ward_room' => 'Basilisa',
                'blood_type' => 'O+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 10,
                'attending_physician' => 'Dr. G. Morales',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Urgent',
                'status' => 'Transfusion Ongoing',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Basilisa Municipal Hospital acute hemorrhage support.',
                'issued_at' => Carbon::parse('2026-05-30 16:45:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0006',
                'patient_name' => 'Angelica Mae Cruz',
                'patient_hrn' => 'HRN-99905',
                'ward_room' => 'Cagdianao',
                'blood_type' => 'A+',
                'blood_component' => 'Fresh Frozen Plasma (FFP)',
                'units_issued' => 6,
                'attending_physician' => 'Dr. M. Santos',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Completed',
                'issued_by' => 'MedTech K. Lopez, RMT',
                'remarks' => 'Cagdianao Health Center quarterly allocation.',
                'issued_at' => Carbon::parse('2026-06-29 13:10:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0007',
                'patient_name' => 'Bernardo Silvestre',
                'patient_hrn' => 'HRN-99918',
                'ward_room' => 'Dinagat',
                'blood_type' => 'O+',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 10,
                'attending_physician' => 'Dr. V. Fernandez',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Issued',
                'issued_by' => 'MedTech M. Cruz, RMT',
                'remarks' => 'Dinagat RHU maternal and pediatric reserve.',
                'issued_at' => Carbon::parse('2026-07-28 08:50:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0008',
                'patient_name' => 'Lourdes Reyes',
                'patient_hrn' => 'HRN-99930',
                'ward_room' => 'Libjo',
                'blood_type' => 'AB+',
                'blood_component' => 'Platelet Concentrate (PC)',
                'units_issued' => 4,
                'attending_physician' => 'Dr. C. Alcantara',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Urgent',
                'status' => 'Issued',
                'issued_by' => 'MedTech A. Salazar, RMT',
                'remarks' => 'Libjo Emergency Clinic thrombocytopenia cases.',
                'issued_at' => Carbon::parse('2026-08-25 15:30:00'),
            ],
            [
                'requisition_no' => 'REQ-2026-0009',
                'patient_name' => 'Francis Domingo',
                'patient_hrn' => 'HRN-99945',
                'ward_room' => 'Loreto',
                'blood_type' => 'B-',
                'blood_component' => 'Packed Red Blood Cells (PRBC)',
                'units_issued' => 3,
                'attending_physician' => 'Dr. R. Navarro',
                'crossmatch_status' => 'Compatible',
                'urgency_level' => 'Routine',
                'status' => 'Completed',
                'issued_by' => 'MedTech M. Cruz, RMT',
                'remarks' => 'Loreto RHU rare Rh-negative reserve units.',
                'issued_at' => Carbon::parse('2026-09-15 11:00:00'),
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
