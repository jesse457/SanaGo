<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class LabResultAttachment extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'lab_result_id',
        'tenant_id',
        'file_path',
        'file_name',
        'file_type',
    ];

    public function labResult()
    {
        return $this->belongsTo(LabResult::class);
    }
}
