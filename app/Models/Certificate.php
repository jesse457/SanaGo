<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'consultation_id', 'issued_by', 'type',
        'serial_number', 'from_date', 'to_date', 'duration_days',
        'diagnosis_summary', 'recommendations', 'employer_name',
        'employer_address', 'status',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date:Y-m-d',
            'to_date' => 'date:Y-m-d',
            'type' => CertificateType::class,
            'status' => CertificateStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $certificate): void {
            if (empty($certificate->serial_number)) {
                $lastSerial = self::whereYear('created_at', now()->year)->max('id') ?? 0;
                $certificate->serial_number = 'CERT-'.now()->year.'-'.str_pad($lastSerial + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class, 'consultation_id');
    }
}
