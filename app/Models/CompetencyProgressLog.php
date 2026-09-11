<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyProgressLog extends Model
{
    protected $fillable = [
        'user_id',
        'competency_id',
        'proficiency_level',
        'source',
        'evidence',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'proficiency_level' => 'integer',
        'recorded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
