<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress_percentage',
        'last_accessed_lesson_id',
        'enrolled_at',
        'completed_at',
        'last_accessed_at',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lastAccessedLesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'last_accessed_lesson_id');
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function recalculateProgress(): int
    {
        $totalLessons = $this->course->total_lessons_count;
        if ($totalLessons === 0) {
            $this->update(['progress_percentage' => 100, 'status' => 'completed', 'completed_at' => now()]);

            return 100;
        }

        $completedLessons = $this->lessonProgress()->where('is_completed', true)->count();
        $percentage = (int) round(($completedLessons / $totalLessons) * 100);

        $updateData = [
            'progress_percentage' => $percentage,
            'last_accessed_at' => now(),
        ];

        if ($percentage >= 100 && $this->status !== 'completed') {
            $updateData['status'] = 'completed';
            $updateData['completed_at'] = now();
        } elseif ($percentage > 0 && $this->status === 'enrolled') {
            $updateData['status'] = 'in_progress';
        }

        $this->update($updateData);

        return $percentage;
    }
}
