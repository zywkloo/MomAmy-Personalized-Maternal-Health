<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observation extends Model
{
    use HasFactory;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'code',
        'system',
        'display',
        'value_type',
        'value',
        'unit',
        'effective_datetime',
        'status',
        'reference_range',
    ];

    protected $casts = [
        'value' => 'array',
        'effective_datetime' => 'datetime',
        'reference_range' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}

