<?php

namespace App\Models;

use App\Enums\LabResultStatus;
use Database\Factories\LabResultFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[Fillable([
    'lab_request_id',
    'status',
    'result_date',
    'results_text',
    'analysis_comments',
    'source',
    'analyzer_name',
    'analyzer_sample_id',
    'analyzer_analytes',
])]
class LabResult extends Model implements HasMedia
{
    /** @use HasFactory<LabResultFactory> */
    use HasFactory, InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'result_date' => 'date',
            'status' => LabResultStatus::class,
            'analyzer_analytes' => 'array',
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

    /** True when the values came off an analyzer rather than a keyboard. */
    public function isFromAnalyzer(): bool
    {
        return $this->source === 'analyzer';
    }

    /** The frames that produced this result, newest first. */
    public function analyzerMessages(): HasMany
    {
        return $this->hasMany(LabAnalyzerMessage::class)->latest('received_at');
    }
}
