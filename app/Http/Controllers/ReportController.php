<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\BloodIssue;
use App\Models\BloodReport;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of blood reports inside and outside Caraga.
     */
    public function index(): Response
    {
        $reports = BloodReport::query()
            ->latest('submitted_at')
            ->get();

        // Intra-Regional Data (Inside Caraga)
        $insideCaragaSummary = [
            'totalDonors' => BloodDonor::count(),
            'totalDonations' => (int) BloodDonor::sum('total_donations'),
            'totalIssues' => (int) BloodIssue::sum('units_issued'),
            'activeMunicipalities' => BloodDonor::distinct('municipality')->count('municipality'),
            'donorsByBloodType' => BloodDonor::select('blood_type')
                ->selectRaw('count(*) as count, sum(total_donations) as total_bags')
                ->groupBy('blood_type')
                ->get(),
            'municipalitiesByProvince' => Municipality::all()->groupBy('province'),
        ];

        // Inter-Regional Data (Outside Caraga)
        $outsideCaragaSummary = [
            'externalReportsCount' => BloodReport::outsideCaraga()->count(),
            'externalUnitsAccounted' => (int) BloodReport::outsideCaraga()->sum('blood_units_count'),
            'partnerFacilitiesCount' => BloodReport::outsideCaraga()->distinct('facility_name')->count('facility_name'),
            'partnerRegionsCount' => BloodReport::outsideCaraga()->distinct('province_region')->count('province_region'),
        ];

        $stats = [
            'totalReports' => BloodReport::count(),
            'insideCaragaReports' => BloodReport::insideCaraga()->count(),
            'outsideCaragaReports' => BloodReport::outsideCaraga()->count(),
            'totalUnitsAccounted' => (int) BloodReport::sum('blood_units_count'),
            'pendingVerifications' => BloodReport::whereIn('status', ['Pending Review', 'Received'])->count(),
            'verifiedReports' => BloodReport::where('status', 'Verified')->count(),
        ];

        $provincesList = [
            'Surigao del Norte (Caraga)',
            'Surigao del Sur (Caraga)',
            'Agusan del Norte (Caraga)',
            'Agusan del Sur (Caraga)',
            'Dinagat Islands (Caraga)',
        ];

        $outsideRegionsList = [
            'Region X - Northern Mindanao (CDO / NMMC)',
            'Region XI - Davao Region (SPMC / Davao Blood Center)',
            'Region VII - Central Visayas (Cebu / VSMMC)',
            'National Capital Region (NCR - Manila DOH / PRC)',
            'Region XII - SOCCSKSARGEN',
            'Region IX - Zamboanga Peninsula',
            'BARMM - Bangsamoro Autonomous Region',
        ];

        $reportTypesList = [
            'Monthly Blood Collection Turnout',
            'Inter-Regional Blood Transfer & Requisition',
            'Hospital Ward Consumption Audit',
            'Serological Safety & Screening Summary',
            'Emergency Disaster Blood Allocation',
            'Mobile Blood Drive Turnout Audit',
        ];

        $count = BloodReport::count() + 1;
        $nextReportCode = 'RPT-CRH-' . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        return Inertia::render('reports/Index', [
            'reports' => $reports,
            'stats' => $stats,
            'insideCaragaSummary' => $insideCaragaSummary,
            'outsideCaragaSummary' => $outsideCaragaSummary,
            'provincesList' => $provincesList,
            'outsideRegionsList' => $outsideRegionsList,
            'reportTypesList' => $reportTypesList,
            'nextReportCode' => $nextReportCode,
        ]);
    }

    /**
     * Store a newly received/logged blood report (Inside or Outside Caraga).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'scope' => 'required|string|in:Inside Caraga,Outside Caraga',
            'facility_name' => 'required|string|max:255',
            'province_region' => 'required|string|max:255',
            'report_type' => 'required|string|max:255',
            'reporting_period' => 'required|string|max:100',
            'blood_units_count' => 'required|integer|min:0|max:10000',
            'blood_breakdown' => 'nullable|string|max:500',
            'submitting_officer' => 'required|string|max:255',
            'status' => 'required|string|in:Verified,Received,Pending Review,Archived',
            'report_code' => 'nullable|string|max:50|unique:blood_reports,report_code',
            'summary_notes' => 'nullable|string|max:2000',
        ]);

        if (empty($validated['report_code'])) {
            $prefix = $validated['scope'] === 'Inside Caraga' ? 'RPT-CRH-' : 'RPT-EXT-';
            $count = BloodReport::count() + 1;
            $validated['report_code'] = $prefix . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            while (BloodReport::where('report_code', $validated['report_code'])->exists()) {
                $count++;
                $validated['report_code'] = $prefix . date('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
        }

        $validated['submitted_at'] = now();

        BloodReport::create($validated);

        return redirect()->back()->with('success', "Blood report {$validated['report_code']} from {$validated['facility_name']} ({$validated['scope']}) recorded successfully.");
    }

    /**
     * Update the specified blood report.
     */
    public function update(Request $request, BloodReport $bloodReport)
    {
        $validated = $request->validate([
            'scope' => 'required|string|in:Inside Caraga,Outside Caraga',
            'facility_name' => 'required|string|max:255',
            'province_region' => 'required|string|max:255',
            'report_type' => 'required|string|max:255',
            'reporting_period' => 'required|string|max:100',
            'blood_units_count' => 'required|integer|min:0|max:10000',
            'blood_breakdown' => 'nullable|string|max:500',
            'submitting_officer' => 'required|string|max:255',
            'status' => 'required|string|in:Verified,Received,Pending Review,Archived',
            'report_code' => 'required|string|max:50|unique:blood_reports,report_code,' . $bloodReport->id,
            'summary_notes' => 'nullable|string|max:2000',
        ]);

        $bloodReport->update($validated);

        return redirect()->back()->with('success', "Report {$bloodReport->report_code} updated successfully.");
    }

    /**
     * Quick status verification toggle.
     */
    public function updateStatus(Request $request, BloodReport $bloodReport)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Verified,Received,Pending Review,Archived',
        ]);

        $bloodReport->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', "Report {$bloodReport->report_code} verification status changed to {$validated['status']}.");
    }

    /**
     * Remove the specified blood report.
     */
    public function destroy(BloodReport $bloodReport)
    {
        $code = $bloodReport->report_code;
        $bloodReport->delete();

        return redirect()->back()->with('success', "Report {$code} removed successfully.");
    }
}
