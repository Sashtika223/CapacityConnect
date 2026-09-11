<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCompetency extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'competency_id',
        'proficiency_level',
        'verified_by',
        'evidence_notes',
        'assessed_at',
    ];

    protected $casts = [
        'proficiency_level' => 'integer',
        'assessed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getProficiencyLevelNameAttribute(): string
    {
        return Competency::levelNames()[$this->proficiency_level] ?? 'Beginner';
    }
}
