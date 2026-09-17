<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function (Request $request) {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('auth/Login', [
        'canResetPassword' => Features::enabled(Features::resetPasswords()),
        'status' => $request->session()->get('status'),
    ]);
})->name('home');

use App\Http\Controllers\BloodCollectionController;
use App\Http\Controllers\BloodConsumptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffAccountController;
use App\Models\BloodDonor;
use App\Models\Municipality;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard', [
            'municipalitiesByProvince' => Municipality::all()->groupBy('province'),
            'totalMunicipalities' => Municipality::count(),
            'totalDonors' => BloodDonor::count(),
            'totalDonations' => (int) BloodDonor::sum('total_donations'),
            'registeredDonorsList' => BloodDonor::latest()->take(10)->get(),
        ]);
    })->name('dashboard');

    Route::get('blood-collection', [BloodCollectionController::class, 'index'])->name('blood-collection');
    Route::post('blood-collection', [BloodCollectionController::class, 'store'])->name('blood-collection.store');
    Route::put('blood-collection/{donor}', [BloodCollectionController::class, 'update'])->name('blood-collection.update');
    Route::post('blood-collection/{donor}/record-donation', [BloodCollectionController::class, 'recordDonation'])->name('blood-collection.record-donation');
    Route::delete('blood-collection/{donor}', [BloodCollectionController::class, 'destroy'])->name('blood-collection.destroy');

    Route::get('blood-consumtion', [BloodConsumptionController::class, 'index'])->name('blood-consumtion');
    Route::post('blood-consumtion', [BloodConsumptionController::class, 'store'])->name('blood-consumtion.store');
    Route::put('blood-consumtion/{bloodIssue}', [BloodConsumptionController::class, 'update'])->name('blood-consumtion.update');
    Route::patch('blood-consumtion/{bloodIssue}/status', [BloodConsumptionController::class, 'updateStatus'])->name('blood-consumtion.update-status');
    Route::delete('blood-consumtion/{bloodIssue}', [BloodConsumptionController::class, 'destroy'])->name('blood-consumtion.destroy');

    Route::get('staff-accounts', [StaffAccountController::class, 'index'])->name('staff-accounts');
    Route::post('staff-accounts', [StaffAccountController::class, 'store'])->name('staff-accounts.store');
    Route::put('staff-accounts/{user}', [StaffAccountController::class, 'update'])->name('staff-accounts.update');
    Route::patch('staff-accounts/{user}/status', [StaffAccountController::class, 'updateStatus'])->name('staff-accounts.update-status');
    Route::delete('staff-accounts/{user}', [StaffAccountController::class, 'destroy'])->name('staff-accounts.destroy');

    Route::get('reports', [ReportController::class, 'index'])->name('reports');
    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');
    Route::put('reports/{bloodReport}', [ReportController::class, 'update'])->name('reports.update');
    Route::patch('reports/{bloodReport}/status', [ReportController::class, 'updateStatus'])->name('reports.update-status');
    Route::delete('reports/{bloodReport}', [ReportController::class, 'destroy'])->name('reports.destroy');
});

require __DIR__.'/settings.php';

