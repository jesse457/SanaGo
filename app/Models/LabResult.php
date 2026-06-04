<?php

namespace App\Models;

use App\Enums\LabResultStatus;
use Database\Factories\LabResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable(['lab_request_id', 'status', 'result_date', 'results_text', 'analysis_comments'])]
class LabResult extends Model implements HasMedia
{
    /** @use HasFactory<LabResultFactory> */
    use HasFactory, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'result_date' => 'date',
            'status' => LabResultStatus::class,
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('report')
            ->acceptsMimeTypes([
                'application/pdf', 'image/jpeg', 'image/png', 'image/gif',
                'text/plain', 'text/csv',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class);
    }
}
