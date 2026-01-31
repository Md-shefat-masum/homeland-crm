<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profession extends Model
{
    protected $guarded = [];

    // Relationships
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'profession_id');
    }
}
