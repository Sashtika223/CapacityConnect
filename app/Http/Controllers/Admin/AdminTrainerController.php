<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TrainerApproved;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * AdminTrainerController
 *
 * Handles all trainer-specific admin operations:
 *   GET  /admin/trainers          → index()   — pending + approved list
 *   GET  /admin/trainers/{user}   → show()    — trainer detail profile
 *   POST /admin/trainers/{user}/approve → approve()
 *   POST /admin/trainers/{user}/reject  → reject()
 *
 * Authorization: All routes are protected by EnsureRole:admin + EnsureActiveAccount
 * middleware in routes/web.php. A 403 Forbidden is returned by EnsureRole if
 * a non-admin user (e.g. a trainer) attempts to call these URLs directly.
 */
class AdminTrainerController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | index() — GET /admin/trainers
    |--------------------------------------------------------------------------
    | Shows pending and approved trainer lists with counters.
    */
    public function index(): View
    {
        $pendingTrainers = User::where('role', 'trainer')
            ->where('status', 'pending')
            ->with(['department', 'profile', 'qualifications', 'experiences'])
            ->latest()
            ->get();

        $approvedTrainers = User::where('role', 'trainer')
            ->where('status', 'approved')
            ->with(['department', 'profile', 'authoredCourses'])
            ->latest('approved_at')
            ->paginate(15);

        $rejectedCount = User::where('role', 'trainer')
            ->where('status', 'rejected')
            ->count();

        return view('admin.users.trainers', compact('pendingTrainers', 'approvedTrainers', 'rejectedCount'));
    }

    /*
    |--------------------------------------------------------------------------
    | show() — GET /admin/trainers/{user}
    |--------------------------------------------------------------------------
    | Shows full trainer profile: personal info, qualifications, experience,
    | authored courses, and approval/rejection actions if still pending.
    */
    public function show(User $user): View
    {
        if ($user->role !== 'trainer') {
            abort(404, 'Trainer not found.');
        }

        $user->load([
            'profile',
            'qualifications',
            'experiences',
            'authoredCourses',
            'department',
            'approvedBy',
        ]);

        return view('admin.users.trainer_detail', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | approve() — POST /admin/trainers/{user}/approve
    |--------------------------------------------------------------------------
    | §11 Approval flow:
    |   1. Verify acting user is admin              (middleware + route gate)
    |   2. Find the trainer                         (route model binding)
    |   3. Verify user has trainer role             (explicit check)
    |   4. Verify current status is pending         (explicit check)
    |   5. Change status: pending → approved        (update)
    |   6. Save approved_at timestamp              (update)
    |   7. Record approving admin's ID             (approved_by)
    |   8. Create audit log entry with old/new     (AuditLogService)
    |   9. Send approval email (fail-safe)         (try/catch — §18)
    |  10. Show success message to admin           (flash)
    */
    public function approve(User $user): RedirectResponse
    {
        // Step 3 — must be a trainer
        if ($user->role !== 'trainer') {
            return back()->with('error', 'This user is not a trainer.');
        }

        // Step 4 — must currently be pending
        if (! $user->isPending()) {
            return back()->with('error', 'This trainer application has already been processed.');
        }

        $previousStatus = $user->status;  // 'pending'

        // Steps 5, 6, 7 — update status + timestamps
        $user->update([
            'status'      => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        // Step 8 — §21 Audit log: trainer ID, admin ID, action, prev/new status
        AuditLogService::log(
            'trainer_approved',
            $user,
            [
                'trainer_id' => $user->id,
                'admin_id'   => auth()->id(),
                'status'     => $previousStatus,
            ],
            [
                'trainer_id'  => $user->id,
                'admin_id'    => auth()->id(),
                'status'      => 'approved',
                'approved_at' => now()->toDateTimeString(),
            ]
        );

        // Send in-app notification (respects user preferences)
        $this->notificationService->send(
            $user,
            'Trainer Account Approved',
            'Your trainer account has been approved. You can now log in to the Capacity Connect Portal.',
            'success',
            route('trainer.dashboard'),
            'admin_alerts'
        );

        // Step 9 — §18 Send email: never roll back approval on failure
        $emailSent  = false;
        $emailError = null;

        try {
            Mail::to($user->email)->send(new TrainerApproved($user));
            $emailSent = true;
        } catch (\Throwable $e) {
            Log::error('[AdminTrainerController] Approval email failed.', [
                'trainer_id'    => $user->id,
                'trainer_email' => $user->email,
                'admin_id'      => auth()->id(),
                'error'         => $e->getMessage(),
            ]);
            $emailError = $e->getMessage();
        }

        // Step 10 — flash message
        if ($emailSent) {
            return redirect()->route('admin.trainers.index')
                ->with('success', "Trainer approved successfully. An approval email has been sent to {$user->email}.");
        }

        return redirect()->route('admin.trainers.index')
            ->with('warning', "Trainer {$user->name} has been approved, but the approval email could not be sent. Please check the mail configuration. (Error: {$emailError})");
    }

    /*
    |--------------------------------------------------------------------------
    | reject() — POST /admin/trainers/{user}/reject
    |--------------------------------------------------------------------------
    | §12 Rejection flow:
    |   - status: pending → rejected
    |   - Records rejected_at, rejected_by, optional rejection_reason
    |   - Audit log with old/new status
    |   - No approval email sent
    */
    public function reject(User $user): RedirectResponse
    {
        if ($user->role !== 'trainer') {
            return back()->with('error', 'This user is not a trainer.');
        }

        if (! $user->isPending()) {
            return back()->with('error', 'This trainer application has already been processed.');
        }

        $previousStatus = $user->status;  // 'pending'

        $user->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejected_by'      => auth()->id(),
            'rejection_reason' => request('rejection_reason'),
        ]);

        // §21 Audit log: full record of the rejection
        AuditLogService::log(
            'trainer_rejected',
            $user,
            [
                'trainer_id' => $user->id,
                'admin_id'   => auth()->id(),
                'status'     => $previousStatus,
            ],
            [
                'trainer_id'       => $user->id,
                'admin_id'         => auth()->id(),
                'status'           => 'rejected',
                'rejected_at'      => now()->toDateTimeString(),
                'rejection_reason' => request('rejection_reason'),
            ]
        );

        return redirect()->route('admin.trainers.index')
            ->with('info', "Trainer {$user->name}'s application has been rejected.");
    }
}
