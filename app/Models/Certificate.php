<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'verification_code',
        'issue_date',
        'expiry_date',
        'status',
        'file_path',
        'revocation_reason',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public static function generateNumber(): string
    {
        return 'CC-'.strtoupper(Str::random(4)).'-'.date('Ymd').'-'.rand(1000, 9999);
    }

    public static function generateVerificationCode(): string
    {
        return hash('sha256', Str::uuid()->toString().microtime());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function isValid(): bool
    {
        if ($this->status !== 'valid') {
            return false;
        }

        // Trainee certificates have permanent / lifetime validity
        if ($this->isTraineeCertificate() || $this->expiry_date === null) {
            return true;
        }

        // Trainer certificates require active validation window
        return $this->expiry_date->isFuture() || $this->expiry_date->isToday();
    }

    public function isExpired(): bool
    {
        if ($this->status === 'expired' && ! $this->isTraineeCertificate()) {
            return true;
        }

        // Trainee certificates do not expire
        if ($this->isTraineeCertificate() || $this->expiry_date === null) {
            return false;
        }

        return $this->expiry_date->isPast() && ! $this->expiry_date->isToday();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        // Trainee certificates do not expire
        if ($this->isTraineeCertificate() || $this->expiry_date === null) {
            return false;
        }

        return $this->isValid() && $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function isTrainerCertificate(): bool
    {
        return $this->user && $this->user->isTrainer();
    }

    public function isTraineeCertificate(): bool
    {
        return $this->user && $this->user->isTrainee();
    }
}
