<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'fhir_id',
        'patient_id',
        'title',
        'type',
        'status',
        'file_path',
        'file_type',
        'file_size',
        'issued_at',
        'conclusion',
        'performers',
        'presented_forms',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'conclusion' => 'array',
        'performers' => 'array',
        'presented_forms' => 'array',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}

