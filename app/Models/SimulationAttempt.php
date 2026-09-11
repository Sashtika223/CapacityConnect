<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimulationAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scenario_id',
        'current_node',
        'path_taken',
        'score',
        'passed',
        'final_feedback',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'path_taken' => 'array',
        'score' => 'integer',
        'passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(SimulationScenario::class, 'scenario_id');
    }
}
