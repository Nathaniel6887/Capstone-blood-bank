<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\BloodIssue;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BloodConsumptionController extends Controller
{
    /**
     * Display a listing of blood consumption / issuances.
     */
    public function index(): Response
    {
        $issues = BloodIssue::query()
            ->latest('issued_at')
            ->get();

        $bloodTypes = ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-', 'B-', 'AB-'];
        $inventory = [];
        foreach ($bloodTypes as $type) {
            $donated = (int) BloodDonor::where('blood_type', $type)->sum('total_donations');
            $issued = (int) BloodIssue::where('blood_type', $type)->where('status', '!=', 'Returned/Discarded')->sum('units_issued');
            $baseStock = match ($type) {
                'O+' => 35,
                'A+' => 25,
                'B+' => 20,
                'AB+' => 12,
                'O-' => 6,
                'A-' => 8,
                'B-' => 7,
                'AB-' => 4,
                default => 10,
            };
            $available = max(0, $baseStock + $donated - $issued);
            $inventory[$type] = $available;
        }

        // Identify lowest stock type
        asort($inventory);
        $lowStockType = array_key_first($inventory) ?? 'O-';
        $lowStockUnits = $inventory[$lowStockType] ?? 0;

        // Active wards summary
        $recentWards = BloodIssue::select('ward_room')
            ->selectRaw('count(*) as total')
            ->groupBy('ward_room')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('ward_room')
            ->toArray();

        $topWardsSummary = !empty($recentWards) 
            ? implode(', ', array_map(fn($w) => explode(' -', $w)[0], $recentWards))
            : 'ICU, ER, Surgery';

        $stats = [
            'unitsIssuedToday' => (int) BloodIssue::whereDate('issued_at', today())->sum('units_issued'),
            'emergencyUnitsToday' => (int) BloodIssue::whereDate('issued_at', today())
                ->where(function ($q) {
                    $q->where('urgency_level', 'Stat/Emergency')
                      ->orWhere('crossmatch_status', 'Emergency Uncrossmatched');
                })->sum('units_issued'),
            'activeWardsCount' => BloodIssue::distinct('ward_room')->count('ward_room'),
            'topWardsSummary' => $topWardsSummary,
            'pendingCrossmatchCount' => BloodIssue::where('crossmatch_status', 'Pending Crossmatch')->count(),
            'lowStockBloodType' => $lowStockType,
            'lowStockUnits' => $lowStockUnits,
            'totalIssuesCount' => BloodIssue::count(),
        ];

        $wardsList = [
            'ICU - Intensive Care Unit',
            'Emergency Trauma & Acute Care',
            'Operating Room / PACU',
            'OB-GYN & Delivery Ward',
            'Surgical Inpatient Ward',
            'Pediatrics & NICU',
            'Internal Medicine Ward',
            'Hemodialysis Center',
            'Oncology & Hematology Unit',
            'Orthopedic Ward',
        ];

        $bloodComponentsList = [
            'Packed Red Blood Cells (PRBC)',
            'Whole Blood (WB)',
            'Fresh Frozen Plasma (FFP)',
            'Platelet Concentrate (PC)',
            'Cryoprecipitate (CRYO)',
        ];

        // Next Requisition Number
        $count = BloodIssue::count() + 1;
        $nextRequisitionNo = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        return Inertia::render('blood-consumtion/Index', [
            'issues' => $issues,
            'stats' => $stats,
            'bloodInventorySummary' => $inventory,
            'wardsList' => $wardsList,
            'bloodComponentsList' => $bloodComponentsList,
            'nextRequisitionNo' => $nextRequisitionNo,
        ]);
    }

    /**
     * Store a newly created blood issuance record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_hrn' => 'required|string|max:50',
            'ward_room' => 'required|string|max:255',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'blood_component' => 'required|string|max:100',
            'units_issued' => 'required|integer|min:1|max:50',
            'attending_physician' => 'required|string|max:255',
            'crossmatch_status' => 'required|string|in:Compatible,Pending Crossmatch,Emergency Uncrossmatched,Incompatible',
            'urgency_level' => 'required|string|in:Routine,Urgent,Stat/Emergency',
            'status' => 'required|string|in:Issued,Transfusion Ongoing,Completed,Returned/Discarded',
            'requisition_no' => 'nullable|string|max:50|unique:blood_issues,requisition_no',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if (empty($validated['requisition_no'])) {
            $count = BloodIssue::count() + 1;
            $validated['requisition_no'] = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            // Ensure unique if conflict exists
            while (BloodIssue::where('requisition_no', $validated['requisition_no'])->exists()) {
                $count++;
                $validated['requisition_no'] = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
        }

        $validated['issued_by'] = auth()->user()->name ?? 'Medical Technologist';
        $validated['issued_at'] = now();

        BloodIssue::create($validated);

        return redirect()->back()->with('success', "Blood unit(s) successfully issued for patient {$validated['patient_name']} under {$validated['requisition_no']}.");
    }

    /**
     * Update the specified blood issuance record.
     */
    public function update(Request $request, BloodIssue $bloodIssue)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'patient_hrn' => 'required|string|max:50',
            'ward_room' => 'required|string|max:255',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'blood_component' => 'required|string|max:100',
            'units_issued' => 'required|integer|min:1|max:50',
            'attending_physician' => 'required|string|max:255',
            'crossmatch_status' => 'required|string|in:Compatible,Pending Crossmatch,Emergency Uncrossmatched,Incompatible',
            'urgency_level' => 'required|string|in:Routine,Urgent,Stat/Emergency',
            'status' => 'required|string|in:Issued,Transfusion Ongoing,Completed,Returned/Discarded',
            'requisition_no' => 'required|string|max:50|unique:blood_issues,requisition_no,' . $bloodIssue->id,
            'remarks' => 'nullable|string|max:1000',
        ]);

        $bloodIssue->update($validated);

        return redirect()->back()->with('success', "Requisition {$bloodIssue->requisition_no} updated successfully.");
    }

    /**
     * Quick status update for transfusion tracking.
     */
    public function updateStatus(Request $request, BloodIssue $bloodIssue)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Issued,Transfusion Ongoing,Completed,Returned/Discarded',
        ]);

        $bloodIssue->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', "Status for {$bloodIssue->requisition_no} changed to {$validated['status']}.");
    }

    /**
     * Remove the specified blood issuance record.
     */
    public function destroy(BloodIssue $bloodIssue)
    {
        $reqNo = $bloodIssue->requisition_no;
        $bloodIssue->delete();

        return redirect()->back()->with('success', "Issuance record {$reqNo} removed successfully.");
    }
}
