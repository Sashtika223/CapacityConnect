<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'job_title',
        'designation',
        'areas_of_expertise',
        'bio',
        'avatar',
        'avatar_path',
        'location',
        'emergency_unit',
        'interests',
        'two_factor_enabled',
    ];

    protected $casts = [
        'interests' => 'array',
        'two_factor_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return the avatar URL — custom upload first, then fallback Gravatar.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/'.$this->avatar_path);
        }

        // Gravatar fallback
        $hash = md5(strtolower(trim($this->user->email ?? '')));

        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=120";
    }

    /**
     * Auto-generate a unique employee ID in the format CC-YYYY-XXXX.
     */
    public static function generateEmployeeId(): string
    {
        $year = now()->year;
        do {
            $candidate = 'CC-'.$year.'-'.strtoupper(Str::random(4));
        } while (static::where('employee_id', $candidate)->exists());

        return $candidate;
    }

    /**
     * Calculate what % of the profile has been filled out.
     */
    public function completionPercentage(User $user): int
    {
        $fields = [
            $user->name,
            $user->email,
            $user->phone,
            $this->job_title,
            $this->bio,
            $this->location,
            $user->department_id,
            $this->avatar_path ?: $this->avatar,
        ];

        $relational = [
            $user->qualifications()->exists(),
            $user->experiences()->exists(),
            $user->skills()->exists(),
            ! empty($this->interests),
        ];

        $filledFields = count(array_filter($fields, fn ($v) => ! empty($v)));
        $filledRelational = count(array_filter($relational));

        $total = count($fields) + count($relational);
        $filled = $filledFields + $filledRelational;

        return (int) round(($filled / $total) * 100);
    }
}
