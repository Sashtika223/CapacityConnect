<?php

namespace App\Http\Controllers;

use App\Models\NotificationPreference;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationPreferenceController extends Controller
{
    public function edit(): View
    {
        $preferences = Auth::user()->notificationPreference()->firstOrCreate(['user_id' => Auth::id()]);

        return view('notifications.preferences', compact('preferences'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'in_app_enabled' => ['boolean'], 'email_enabled' => ['boolean'],
            'course_updates' => ['boolean'], 'assessment_results' => ['boolean'],
            'certificate_updates' => ['boolean'], 'announcements' => ['boolean'],
            'recommendations' => ['boolean'], 'admin_alerts' => ['boolean'],
        ]);
        $values = [];
        foreach (array_keys($validated + ['in_app_enabled' => false, 'email_enabled' => false]) as $key) {
            $values[$key] = $request->boolean($key);
        }
        $preference = NotificationPreference::updateOrCreate(['user_id' => Auth::id()], $values);
        AuditLogService::log('notification_preferences.updated', $preference);

        return back()->with('success', 'Notification preferences updated.');
    }
}
