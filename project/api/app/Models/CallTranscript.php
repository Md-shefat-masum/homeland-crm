<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallTranscript extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_edited' => 'boolean',
        'confidence_score' => 'decimal:2',
        'processed_at' => 'datetime',
        'sync_at' => 'datetime',
    ];

    // Relationships
    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class, 'call_log_id');
    }
}
