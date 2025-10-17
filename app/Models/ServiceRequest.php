<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'code',
        'system',
        'display',
        'status',
        'intent',
        'authored_on',
        'performer',
        'reason',
        'notes',
    ];

    protected $casts = [
        'authored_on' => 'datetime',
        'performer' => 'array',
        'reason' => 'array',
        'notes' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}

