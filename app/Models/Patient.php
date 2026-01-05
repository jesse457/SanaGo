<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\Transformation\Lowercase;
use ParagonIE\CipherSweet\Transformation\Trigram; // <--- Import Trigram
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Patient extends Model implements CipherSweetEncrypted
{
    use BelongsToTenant, HasFactory, UsesCipherSweet;

    protected $fillable = [
        'patient_uid',
        'first_name',
        'last_name',
        'phone',
        'email',
        'address',
        'referral_note_path',
        'age',
        'gender',
        'is_admitted_approve',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    /**
     * Accessor to combine First and Last name.
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Configure CipherSweet encryption.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            // 1. First Name: Case-insensitive + Partial Match
            ->addField('first_name')
            ->addBlindIndex('first_name', new BlindIndex('first_name_index', [
                new Lowercase, // Normalize to lowercase
            ]))

            // 2. Last Name: Case-insensitive + Partial Match
            ->addField('last_name')
            ->addBlindIndex('last_name', new BlindIndex('last_name_index', [
                new Lowercase,
            ]))

            // 3. Email: Case-insensitive (Exact match usually preferred for emails)
            ->addField('email')
            ->addBlindIndex('email', new BlindIndex('email_index', [
                new Lowercase,
            ]))

            // 4. Phone: Encrypted, Exact Match
            ->addField('phone')
            ->addBlindIndex('phone', new BlindIndex('phone_index'))

            // 6. Address: Encrypted only (Not searchable)
            ->addField('address')

            // 7. Optional fields
            ->addOptionalTextField('id_document_path')
            ->addOptionalTextField('referral_note_path');
    }

    // -----------------------
    // Relationships & Scopes
    // -----------------------

    public function scopeFuture($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function vitals()
    {
        return $this->hasMany(Vital::class);
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function supplyUsages()
    {
        return $this->hasMany(SupplyUsage::class);
    }
}
