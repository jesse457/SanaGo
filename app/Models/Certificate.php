<?php

namespace App\Models;

use App\Enums\CertificateStatus;
use App\Enums\CertificateType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

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

    /**
     * Serial numbers are per year and must stay unique.
     *
     * The previous implementation reused `max('id')`, which regresses when the
     * newest certificate is deleted and then collides with the unique index.
     * Counting the existing rows for the year and retrying keeps the sequence
     * gapless enough while still guaranteeing uniqueness.
     */
    protected static function booted(): void
    {
        static::creating(function (self $certificate): void {
            if (empty($certificate->serial_number)) {
                $certificate->serial_number = self::nextSerialNumber();
            }
        });
    }

    /**
     * Build the next free serial number for the current year.
     */
    public static function nextSerialNumber(): string
    {
        $year = now()->year;
        $prefix = 'CERT-'.$year.'-';

        $next = self::whereYear('created_at', $year)->count() + 1;

        // Guard against gaps left by deleted rows: advance until the candidate
        // is actually unused, capped so a corrupt table cannot loop forever.
        for ($attempt = 0; $attempt < 1000; $attempt++, $next++) {
            $candidate = $prefix.str_pad((string) $next, 4, '0', STR_PAD_LEFT);

            if (! self::where('serial_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        return $prefix.Str::ulid();
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
