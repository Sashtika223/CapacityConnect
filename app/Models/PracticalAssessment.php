<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PracticalAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'competency_id',
        'title',
        'scenario_prompt',
        'evaluation_criteria',
        'max_score',
        'passing_score',
    ];

    protected $casts = [
        'max_score' => 'integer',
        'passing_score' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(PracticalSubmission::class);
    }
}
