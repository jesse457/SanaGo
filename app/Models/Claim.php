<?php

namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'payer_id', 'patient_insurance_id',
        'claim_amount', 'submitted_at', 'status',
        'rejection_reason', 'paid_at', 'remittance_reference',
    ];

    protected function casts(): array
    {
        return [
            'claim_amount' => 'decimal:2',
            'submitted_at' => 'datetime',
            'paid_at' => 'datetime',
            'status' => ClaimStatus::class,
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(Payer::class);
    }

    public function patientInsurance(): BelongsTo
    {
        return $this->belongsTo(PatientInsurance::class);
    }
}
