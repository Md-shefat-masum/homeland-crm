<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'info' => 'array',
    ];

    // Relationships
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class, 'profession_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function currentAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'current_address_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'customer_id');
    }

    public function customerNotes(): HasMany
    {
        return $this->hasMany(CustomerNote::class, 'customer_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class, 'customer_id');
    }

    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class, 'customer_id');
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class, 'customer_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'customer_id');
    }

    public function customerAssignments(): HasMany
    {
        return $this->hasMany(CustomerAssignment::class, 'customer_id');
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
