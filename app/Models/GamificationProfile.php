<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GamificationProfile extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'level',
    ];

    protected $attributes = [
        'points' => 0,
        'level' => 1,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(GamificationEvent::class, 'user_id', 'user_id');
    }
}
