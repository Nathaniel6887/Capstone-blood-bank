<?php

namespace App\Http\Controllers;

use App\Models\BloodDonor;
use App\Models\Municipality;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BloodCollectionController extends Controller
{
    /**
     * Display a listing of blood donor collections.
     */
    public function index(): Response
    {
        $donors = BloodDonor::query()
            ->latest()
            ->get();

        $municipalities = Municipality::query()
            ->orderBy('province')
            ->orderBy('name')
            ->get()
            ->groupBy('province');

        return Inertia::render('blood-collection/Index', [
            'donors' => $donors,
            'municipalities' => $municipalities,
            'stats' => [
                'totalDonors' => BloodDonor::count(),
                'totalDonations' => (int) BloodDonor::sum('total_donations'),
                'eligibleDonors' => BloodDonor::where('age', '>=', 18)->where('age', '<=', 65)->count(),
                'activeLocations' => BloodDonor::distinct('municipality')->count('municipality'),
            ],
        ]);
    }

    /**
     * Store a newly created blood donor collection.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'age' => 'required|integer|min:16|max:80',
            'gender' => 'required|string|max:50',
            'barangay' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'contact_information' => 'required|string|max:100',
            'total_donations' => 'nullable|integer|min:1',
        ]);

        if (empty($validated['total_donations'])) {
            $validated['total_donations'] = 1;
        }

        BloodDonor::create($validated);

        return redirect()->back()->with('success', 'New blood donor collection record registered successfully.');
    }

    /**
     * Update the specified donor collection.
     */
    public function update(Request $request, BloodDonor $donor)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'blood_type' => 'required|string|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'age' => 'required|integer|min:16|max:80',
            'gender' => 'required|string|max:50',
            'barangay' => 'required|string|max:255',
            'municipality' => 'required|string|max:255',
            'contact_information' => 'required|string|max:100',
            'total_donations' => 'required|integer|min:1',
        ]);

        $donor->update($validated);

        return redirect()->back()->with('success', 'Donor record updated successfully.');
    }

    /**
     * Increment donation count for existing donor.
     */
    public function recordDonation(BloodDonor $donor)
    {
        $donor->increment('total_donations');

        return redirect()->back()->with('success', 'New donation recorded for ' . $donor->full_name . '. Total donations: ' . $donor->total_donations);
    }

    /**
     * Remove the specified donor record.
     */
    public function destroy(BloodDonor $donor)
    {
        $donor->delete();

        return redirect()->back()->with('success', 'Donor collection record removed successfully.');
    }
}
