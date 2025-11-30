<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class MedicalRecordAttachment extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'medical_record_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}
