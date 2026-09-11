<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAnnouncementController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(): View
    {
        $announcements = Announcement::with('user')->latest('published_at')->paginate(15);

        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_role' => 'required|in:all,trainee,trainer,admin',
            'is_pinned' => 'nullable|boolean',
        ]);

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target_role' => $validated['target_role'],
            'is_pinned' => $request->boolean('is_pinned'),
            'published_at' => now(),
        ]);

        $recipients = User::where('status', 'active')
            ->when($announcement->target_role !== 'all', fn ($query) => $query->where('role', $announcement->target_role))
            ->get();
        foreach ($recipients as $recipient) {
            $this->notificationService->send($recipient, 'New Announcement', $announcement->title, 'info', route('home'), 'announcements');
        }

        AuditLogService::log('announcement.created', $announcement, null, ['title' => $announcement->title]);

        return back()->with('success', 'Platform bulletin announcement published successfully!');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();
        AuditLogService::log('announcement.deleted', null, null, ['id' => $announcement->id]);

        return back()->with('info', 'Announcement removed.');
    }
}
