<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class StaffAccountController extends Controller
{
    /**
     * Display a listing of staff accounts.
     */
    public function index(): Response
    {
        $users = User::query()
            ->latest()
            ->get();

        $rolesList = [
            'System Administrator',
            'Registered Medical Technologist (RMT)',
            'Laboratory Supervisor',
            'Blood Bank Officer',
            'Phlebotomist / Donor Care',
            'Quality Assurance Officer',
            'Transfusion Safety Nurse',
        ];

        $departmentsList = [
            'Blood Bank & Transfusion Medicine',
            'Laboratory & Clinical Pathology',
            'Donor Recruitment & Mobile Collection',
            'Hematology & Immunohematology Section',
            'Quality Assurance & Component Processing',
        ];

        $stats = [
            'totalStaff' => User::count(),
            'totalAdmins' => User::where('role', 'System Administrator')->count(),
            'totalMedTechs' => User::where('role', 'like', '%Medical Technologist%')
                ->orWhere('role', 'like', '%RMT%')
                ->count(),
            'activeStaff' => User::where('status', 'Active')->count(),
            'onLeaveStaff' => User::where('status', 'On Leave')->count(),
        ];

        $count = User::count() + 1;
        $nextEmployeeId = 'CRH-EMP-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        return Inertia::render('staff-accounts/Index', [
            'users' => $users,
            'stats' => $stats,
            'rolesList' => $rolesList,
            'departmentsList' => $departmentsList,
            'nextEmployeeId' => $nextEmployeeId,
            'currentUserId' => auth()->id(),
        ]);
    }

    /**
     * Store a newly created staff account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|max:100',
            'employee_id' => 'nullable|string|max:50|unique:users,employee_id',
            'department' => 'required|string|max:150',
            'phone_number' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive,On Leave',
        ]);

        if (empty($validated['employee_id'])) {
            $count = User::count() + 1;
            $validated['employee_id'] = 'CRH-EMP-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
            while (User::where('employee_id', $validated['employee_id'])->exists()) {
                $count++;
                $validated['employee_id'] = 'CRH-EMP-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
            }
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->back()->with('success', "Staff account for {$validated['name']} registered successfully.");
    }

    /**
     * Update the specified staff account.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|string|max:100',
            'employee_id' => 'required|string|max:50|unique:users,employee_id,' . $user->id,
            'department' => 'required|string|max:150',
            'phone_number' => 'nullable|string|max:50',
            'status' => 'required|string|in:Active,Inactive,On Leave',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', "Staff account for {$user->name} updated successfully.");
    }

    /**
     * Quick update of staff account status.
     */
    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Active,Inactive,On Leave',
        ]);

        $user->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', "Status for {$user->name} changed to {$validated['status']}.");
    }

    /**
     * Remove the specified staff account.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own logged-in administrator account.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Staff account for {$userName} removed successfully.");
    }
}
