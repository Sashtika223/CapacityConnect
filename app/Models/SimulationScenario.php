<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SimulationScenario extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'disaster_type',
        'description',
        'initial_situation',
        'decision_tree_data',
        'difficulty',
        'target_competency_id',
        'time_limit_minutes',
        'passing_score',
        'is_active',
    ];

    protected $casts = [
        'decision_tree_data' => 'array',
        'time_limit_minutes' => 'integer',
        'passing_score' => 'integer',
        'is_active' => 'boolean',
    ];

    public function targetCompetency(): BelongsTo
    {
        return $this->belongsTo(Competency::class, 'target_competency_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(SimulationAttempt::class, 'scenario_id');
    }
}
