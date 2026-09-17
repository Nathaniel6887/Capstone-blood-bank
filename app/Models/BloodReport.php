<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodReport extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blood_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'report_code',
        'scope',
        'facility_name',
        'province_region',
        'report_type',
        'reporting_period',
        'blood_units_count',
        'blood_breakdown',
        'submitting_officer',
        'status',
        'summary_notes',
        'submitted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'blood_units_count' => 'integer',
        'submitted_at' => 'datetime',
    ];

    /**
     * Scope a query to only include reports inside Caraga Region.
     */
    public function scopeInsideCaraga($query)
    {
        return $query->where('scope', 'Inside Caraga');
    }

    /**
     * Scope a query to only include reports outside Caraga Region.
     */
    public function scopeOutsideCaraga($query)
    {
        return $query->where('scope', 'Outside Caraga');
    }
}
