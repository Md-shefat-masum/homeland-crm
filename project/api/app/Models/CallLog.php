<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CallLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_recorded' => 'boolean',
        'auto_opened_app' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'recording_consent_at' => 'datetime',
        'sync_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function transcript(): HasOne
    {
        return $this->hasOne(CallTranscript::class, 'call_log_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customerAssignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class, 'call_log_id');
    }
}
