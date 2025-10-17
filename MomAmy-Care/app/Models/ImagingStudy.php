<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImagingStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'study_uid',
        'modality',
        'description',
        'started_at',
        'series',
        'identifiers',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'series' => 'array',
        'identifiers' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}

