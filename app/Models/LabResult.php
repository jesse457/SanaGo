<?php

namespace App\Models;

use App\Traits\TracksRevenue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\EncryptedRow;
use ParagonIE\CipherSweet\Transformation\Lowercase;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class LabResult extends Model implements CipherSweetEncrypted
{
    use BelongsToTenant, HasFactory, TracksRevenue,UsesCipherSweet;

    protected $fillable = [

        'lab_request_id',
        'lab_technician_id',
        'doctor_id',
        'consultation_id',
        'result_date',
        'results_text',
        'analysis_comments',
        'status',
        'price',
    ];

    protected $casts = [
        'result_date' => 'datetime',
    ];
    /**
     * Configure CipherSweet encryption for this model.
     *
     * Fields added with ->addField(...) should correspond to text columns
     * in your DB to store ciphertext. For exact-match searchable fields,
     * add a BlindIndex.
     */
    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            // 1. First Name: Case-insensitive + Partial Match
            ->addField('first_name')

            // 2. Last Name: Case-insensitive + Partial Match
            ->addField('last_name');



    }
    public function labRequest()
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function labTechnician()
    {
        return $this->belongsTo(User::class, 'lab_technician_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function consultation()
    {
        return $this->belongsTo(MedicalRecord::class, 'consultation_id');
    }

    public function attachments()
    {
        return $this->hasMany(LabResultAttachment::class);
    }
}
