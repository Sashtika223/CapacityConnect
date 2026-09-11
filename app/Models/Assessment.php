<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'module_id',
        'title',
        'description',
        'type',
        'time_limit_minutes',
        'passing_percentage',
        'max_attempts',
        'negative_marks_per_question',
        'is_randomized',
        'is_active',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'passing_percentage' => 'integer',
        'max_attempts' => 'integer',
        'negative_marks_per_question' => 'float',
        'is_randomized' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(AssessmentAttempt::class);
    }

    public function getTotalMarksAttribute(): float
    {
        return (float) $this->questions->sum('marks');
    }
}
