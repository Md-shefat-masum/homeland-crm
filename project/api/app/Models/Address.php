<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'depth' => 'integer',
        'sort_order' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Address::class, 'parent_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'current_address_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'address_id');
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
