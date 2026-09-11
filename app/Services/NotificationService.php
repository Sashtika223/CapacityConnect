<?php

namespace App\Services;

use App\Models\CustomNotification;
use App\Models\User;
use App\Notifications\PlatformNotification;

class NotificationService
{
    public function send(User $recipient, string $title, string $message, string $type = 'info', ?string $actionUrl = null, string $preference = 'admin_alerts'): void
    {
        $preferences = $recipient->notificationPreference()->firstOrCreate(['user_id' => $recipient->id]);
        $preferences->refresh();

        if ($preferences->in_app_enabled && $preferences->{$preference}) {
            CustomNotification::create([
                'user_id' => $recipient->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'action_url' => $actionUrl,
            ]);
        }

        if ($preferences->email_enabled && $preferences->{$preference} && filled($recipient->email) && config('mail.default')) {
            $recipient->notify(new PlatformNotification($title, $message, $actionUrl));
        }
    }
}
