<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pricing extends Model
{
    protected $table = 'pricing';

    protected $guarded = [];

    protected $casts = [
        'quoted_price' => 'decimal:2',
        'customer_budget' => 'decimal:2',
        'agreeable_price' => 'decimal:2',
    ];

    // Relationships
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
