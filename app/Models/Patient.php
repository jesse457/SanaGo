<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
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
     * Allows $patient->name to work in the view.
     */
    public function getNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
    /**
     * Configure CipherSweet encryption for this model.
     *
     * Fields added with ->addField(...) should correspond to text columns
     * in your DB to store ciphertext. For exact-match searchable fields,
     * add a BlindIndex.
     *
     * @param EncryptedRow $encryptedRow
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            // first & last names - encrypted and searchable (exact match)
            ->addField('first_name')
            ->addBlindIndex('first_name', new BlindIndex('first_name_index'))
            ->addField('last_name')
            ->addBlindIndex('last_name', new BlindIndex('last_name_index'))
            ->addField('email')
            ->addBlindIndex('email', new BlindIndex('email_index'))
            // phone & email - encrypted and searchable (exact match)
            ->addField('phone')
            // other sensitive fields (encrypted, not necessarily searchable)
            ->addField('address')
            ->addOptionalTextField('id_document_path')
            ->addOptionalTextField('referral_note_path');

        // If you want case-insensitive or transformed blind indexes, define
        // transformations via BlindIndex options (see CipherSweet docs).
    }

    // -----------------------
    // relationships & scopes
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
