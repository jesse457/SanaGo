<?php

namespace App\Models;

use App\Enums\PayerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'contact_person', 'phone', 'email',
        'coverage_percentage', 'reimbursement_days', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => PayerType::class,
            'coverage_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function patientInsurances(): HasMany
    {
        return $this->hasMany(PatientInsurance::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }
}
