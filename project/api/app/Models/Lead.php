<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'next_contact_date' => 'date',
        'sync_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function interestedType(): BelongsTo
    {
        return $this->belongsTo(InterestedType::class, 'interested_type_id');
    }

    public function customerLeadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(Pricing::class, 'lead_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(CustomerNote::class, 'lead_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'lead_id');
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'lead_id');
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'lead_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'lead_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
