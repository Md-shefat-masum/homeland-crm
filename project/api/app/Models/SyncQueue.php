<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyncQueue extends Model
{
    protected $table = 'sync_queue';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'next_retry_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(SyncQueue::class, 'depends_on_queue_id');
    }

    public function dependentItems(): HasMany
    {
        return $this->hasMany(SyncQueue::class, 'depends_on_queue_id');
    }
}
