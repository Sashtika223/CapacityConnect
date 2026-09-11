<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id', 'in_app_enabled', 'email_enabled', 'course_updates',
        'assessment_results', 'certificate_updates', 'announcements',
        'recommendations', 'admin_alerts',
    ];

    protected $casts = [
        'in_app_enabled' => 'boolean', 'email_enabled' => 'boolean',
        'course_updates' => 'boolean', 'assessment_results' => 'boolean',
        'certificate_updates' => 'boolean', 'announcements' => 'boolean',
        'recommendations' => 'boolean', 'admin_alerts' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
