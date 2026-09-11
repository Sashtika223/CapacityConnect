<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticalSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'practical_assessment_id',
        'user_id',
        'submission_text',
        'file_path',
        'status',
        'score',
        'feedback',
        'reviewer_id',
        'reviewed_at',
    ];

    protected $casts = [
        'score' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function practicalAssessment(): BelongsTo
    {
        return $this->belongsTo(PracticalAssessment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
