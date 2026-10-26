<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Every frame an analyzer sent, kept verbatim.
 *
 * A lab can be asked to justify a value months later; this is the record of
 * what the machine actually transmitted, independent of what a human edited
 * afterwards. `message_hash` makes re-sent frames idempotent.
 */
#[Fillable([
    'message_hash',
    'format',
    'analyzer_name',
    'analyzer_serial',
    'sample_id',
    'status',
    'raw_payload',
    'parsed',
    'notes',
    'lab_request_id',
    'lab_result_id',
    'imported_by',
    'received_at',
])]
class LabAnalyzerMessage extends Model
{
    /** Stored but no order matched it. */
    public const STATUS_UNMATCHED = 'unmatched';

    /** Written onto the order it matched. */
    public const STATUS_IMPORTED = 'imported';

    /** Could not be read as any supported format. */
    public const STATUS_REJECTED = 'rejected';

    protected function casts(): array
    {
        return [
            'parsed' => 'array',
            'received_at' => 'datetime',
        ];
    }

    public function labRequest(): BelongsTo
    {
        return $this->belongsTo(LabRequest::class);
    }

    public function labResult(): BelongsTo
    {
        return $this->belongsTo(LabResult::class);
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
