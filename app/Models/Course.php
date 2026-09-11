<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'department_id',
        'title',
        'slug',
        'short_description',
        'full_description',
        'category',
        'difficulty',
        'duration_hours',
        'thumbnail',
        'learning_objectives',
        'prerequisites',
        'completion_criteria',
        'status',
        'rejection_reason',
        'published_at',
    ];

    protected $casts = [
        'learning_objectives' => 'array',
        'prerequisites' => 'array',
        'published_at' => 'datetime',
        'duration_hours' => 'integer',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class, 'course_id', 'module_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function practicalAssessments(): HasMany
    {
        return $this->hasMany(PracticalAssessment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'course_competencies')
            ->withPivot('target_level')
            ->withTimestamps();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function getTotalLessonsCountAttribute(): int
    {
        return $this->modules->sum(fn ($m) => $m->lessons->count());
    }
}
