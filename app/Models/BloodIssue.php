<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodIssue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blood_issues';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'requisition_no',
        'patient_name',
        'patient_hrn',
        'ward_room',
        'blood_type',
        'blood_component',
        'units_issued',
        'attending_physician',
        'crossmatch_status',
        'urgency_level',
        'status',
        'issued_by',
        'remarks',
        'issued_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'units_issued' => 'integer',
        'issued_at' => 'datetime',
    ];
}
