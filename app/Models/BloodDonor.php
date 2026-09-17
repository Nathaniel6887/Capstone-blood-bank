<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodDonor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'blood_type',
        'age',
        'gender',
        'barangay',
        'municipality',
        'contact_information',
        'total_donations',
    ];

    protected $casts = [
        'age' => 'integer',
        'total_donations' => 'integer',
    ];
}
