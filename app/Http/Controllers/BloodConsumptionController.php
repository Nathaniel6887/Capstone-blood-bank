<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\BloodIssue;
use App\Models\Municipality;
use Carbon\Carbon;
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

        // Active wards / locations summary
        $recentWards = BloodIssue::select('ward_room')
            ->selectRaw('count(*) as total')
            ->groupBy('ward_room')
            ->orderByDesc('total')
            ->limit(3)
            ->pluck('ward_room')
            ->toArray();

        $topWardsSummary = !empty($recentWards) 
            ? implode(', ', array_map(fn($w) => explode(' -', $w)[0], $recentWards))
            : 'Tubajon, San Jose, Basilisa';

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
            'totalBagsCount' => (int) BloodIssue::sum('units_issued'),
        ];

        $wardsList = [
            'Tubajon',
            'San Jose',
            'Basilisa',
            'Cagdianao',
            'Dinagat',
            'Libjo',
            'Loreto',
            'ICU - Intensive Care Unit',
            'Emergency Trauma & Acute Care',
            'Operating Room / PACU',
            'OB-GYN & Delivery Ward',
            'Surgical Inpatient Ward',
            'Pediatrics & NICU',
            'Internal Medicine Ward',
            'Hemodialysis Center',
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
            'municipalities' => Municipality::all()->groupBy('province'),
        ]);
    }

    /**
     * Store a newly created blood issuance record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'nullable|string|max:255',
            'patient_hrn' => 'nullable|string|max:50',
            'ward_room' => 'required|string|max:255',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'blood_component' => 'nullable|string|max:100',
            'units_issued' => 'required|integer|min:1|max:100',
            'attending_physician' => 'nullable|string|max:255',
            'crossmatch_status' => 'nullable|string|in:Compatible,Pending Crossmatch,Emergency Uncrossmatched,Incompatible',
            'urgency_level' => 'nullable|string|in:Routine,Urgent,Stat/Emergency',
            'status' => 'nullable|string|in:Issued,Transfusion Ongoing,Completed,Returned/Discarded',
            'requisition_no' => 'nullable|string|max:50|unique:blood_issues,requisition_no',
            'remarks' => 'nullable|string|max:1000',
            'issued_at' => 'nullable|date',
        ]);

        if (empty($validated['requisition_no'])) {
            $count = BloodIssue::count() + 1;
            $validated['requisition_no'] = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            while (BloodIssue::where('requisition_no', $validated['requisition_no'])->exists()) {
                $count++;
                $validated['requisition_no'] = 'REQ-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
        }

        $validated['patient_name'] = $validated['patient_name'] ?? 'Patient - ' . $validated['ward_room'];
        $validated['patient_hrn'] = $validated['patient_hrn'] ?? 'HRN-' . rand(10000, 99999);
        $validated['blood_component'] = $validated['blood_component'] ?? 'Packed Red Blood Cells (PRBC)';
        $validated['attending_physician'] = $validated['attending_physician'] ?? 'Dr. G. Morales, MD';
        $validated['crossmatch_status'] = $validated['crossmatch_status'] ?? 'Compatible';
        $validated['urgency_level'] = $validated['urgency_level'] ?? 'Routine';
        $validated['status'] = $validated['status'] ?? 'Issued';
        $validated['issued_by'] = auth()->user()->name ?? 'Medical Technologist';
        $validated['issued_at'] = !empty($validated['issued_at']) ? Carbon::parse($validated['issued_at']) : now();

        BloodIssue::create($validated);

        return redirect()->back()->with('success', "Blood consumption record created for {$validated['ward_room']} ({$validated['units_issued']} bags of {$validated['blood_type']}).");
    }

    /**
     * Update the specified blood issuance record.
     */
    public function update(Request $request, BloodIssue $bloodIssue)
    {
        $validated = $request->validate([
            'patient_name' => 'nullable|string|max:255',
            'patient_hrn' => 'nullable|string|max:50',
            'ward_room' => 'required|string|max:255',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'blood_component' => 'nullable|string|max:100',
            'units_issued' => 'required|integer|min:1|max:100',
            'attending_physician' => 'nullable|string|max:255',
            'crossmatch_status' => 'nullable|string|in:Compatible,Pending Crossmatch,Emergency Uncrossmatched,Incompatible',
            'urgency_level' => 'nullable|string|in:Routine,Urgent,Stat/Emergency',
            'status' => 'nullable|string|in:Issued,Transfusion Ongoing,Completed,Returned/Discarded',
            'requisition_no' => 'required|string|max:50|unique:blood_issues,requisition_no,' . $bloodIssue->id,
            'remarks' => 'nullable|string|max:1000',
            'issued_at' => 'nullable|date',
        ]);

        if (!empty($validated['issued_at'])) {
            $validated['issued_at'] = Carbon::parse($validated['issued_at']);
        }

        $bloodIssue->update($validated);

        return redirect()->back()->with('success', "Record {$bloodIssue->requisition_no} updated successfully.");
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

        return redirect()->back()->with('success', "Record {$reqNo} removed successfully.");
    }
}
