<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIContentGeneration extends Model
{
    use HasFactory;

    protected $table = 'ai_content_generations';

    protected $fillable = [
        'user_id',
        'topic',
        'type',
        'difficulty',
        'competency_id',
        'course_id',
        'prompt',
        'generated_content',
        'status',
        'reviewer_id',
        'rejection_reason',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
