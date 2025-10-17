<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fhir_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'contact_phone',
        'email',
        'blood_type',
        'rh_factor',
        'allergies',
        'address',
        'gravidity',
        'parity',
        'estimated_due_date',
        'last_menstrual_period',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'estimated_due_date' => 'date',
        'last_menstrual_period' => 'date',
        'allergies' => 'array',
        'address' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function imagingStudies(): HasMany
    {
        return $this->hasMany(ImagingStudy::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class);
    }
}

