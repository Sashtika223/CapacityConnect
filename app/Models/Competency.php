<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'category',
        'description',
        'required_level',
        'department_id',
    ];

    public static function levelNames(): array
    {
        return [
            1 => 'Beginner',
            2 => 'Basic',
            3 => 'Intermediate',
            4 => 'Advanced',
            5 => 'Expert',
        ];
    }

    public function getRequiredLevelNameAttribute(): string
    {
        return self::levelNames()[$this->required_level] ?? 'Intermediate';
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_competencies')
            ->withPivot('proficiency_level', 'verified_by', 'evidence_notes', 'assessed_at')
            ->withTimestamps();
    }

    public function userCompetencies(): HasMany
    {
        return $this->hasMany(UserCompetency::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_competencies')
            ->withPivot('target_level')
            ->withTimestamps();
    }
}
