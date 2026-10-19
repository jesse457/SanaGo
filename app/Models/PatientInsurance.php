<?php

namespace App\Models;

use App\Enums\BeneficiaryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatientInsurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'payer_id', 'policy_number', 'card_number',
        'valid_from', 'valid_until', 'beneficiary_type', 'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'beneficiary_type' => BeneficiaryType::class,
            'is_primary' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(Payer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
}
