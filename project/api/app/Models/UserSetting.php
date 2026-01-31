<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'call_recording_enabled' => 'boolean',
        'speech_to_text_enabled' => 'boolean',
        'app_lock_enabled' => 'boolean',
        'app_lock_timeout_seconds' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
