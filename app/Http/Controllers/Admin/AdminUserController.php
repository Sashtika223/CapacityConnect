<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TrainerApproved;
use App\Models\Department;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): View
    {
        $role = $request->input('role', 'all');
        $status = $request->input('status', 'all');
        $search = $request->input('search');

        $query = User::with(['department', 'profile']);

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'role', 'status', 'departments'));
    }

    public function trainers(): View
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

    public function approveTrainer(User $user): RedirectResponse
    {
        if ($user->role !== 'trainer') {
            return back()->with('error', 'User is not a trainer.');
        }

        if (! $user->isPending()) {
            return back()->with('error', 'This trainer application has already been processed.');
        }

        $user->update([
            'status'      => 'approved',  // canonical approved status for trainers
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        AuditLogService::log('trainer.approved', $user);

        // Send in-app notification (respects user preferences)
        $this->notificationService->send(
            $user,
            'Trainer Account Approved',
            'Your trainer account has been approved. You can now log in to the Capacity Connect Portal.',
            'success',
            route('trainer.dashboard'),
            'admin_alerts'
        );

        // §18 — Send approval email. Wrap in try/catch:
        // Approval status is kept regardless of email failure (they are separate concerns).
        $emailSent = false;
        $emailError = null;
        try {
            Mail::to($user->email)->send(new TrainerApproved($user));
            $emailSent = true;
        } catch (\Throwable $e) {
            // Log the failure but do NOT roll back the approval
            Log::error('[TrainerApproval] Approval email failed to send.', [
                'trainer_id'    => $user->id,
                'trainer_email' => $user->email,
                'error'         => $e->getMessage(),
            ]);
            $emailError = $e->getMessage();
        }

        if ($emailSent) {
            return back()->with('success', "Trainer {$user->name} has been approved. An approval email has been sent to {$user->email}.");
        }

        return back()->with('warning', "Trainer {$user->name} has been approved successfully, but the approval email could not be sent. Please check the mail configuration. (Error: {$emailError})");
    }

    public function rejectTrainer(User $user): RedirectResponse
    {
        if ($user->role !== 'trainer') {
            return back()->with('error', 'User is not a trainer.');
        }

        if (! $user->isPending()) {
            return back()->with('error', 'This trainer application has already been processed.');
        }

        $user->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejected_by'      => auth()->id(),
            'rejection_reason' => request('rejection_reason'),
        ]);

        AuditLogService::log('trainer.rejected', $user);

        return back()->with('info', "Trainer {$user->name}'s application has been rejected.");
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->isAdmin() && $user->id === 1) {
            return back()->with('error', 'Cannot deactivate the root Administrator.');
        }

        $oldStatus = $user->status;

        // Trainers use 'approved'/'suspended'; other roles use 'active'/'suspended'
        if ($user->isTrainer()) {
            $newStatus = ($user->status === 'approved') ? 'suspended' : 'approved';
        } else {
            $newStatus = ($user->status === 'active') ? 'suspended' : 'active';
        }

        $user->update(['status' => $newStatus]);

        AuditLogService::log(
            'user.status_toggled',
            $user,
            ['status' => $oldStatus, 'admin_id' => auth()->id()],
            ['status' => $newStatus, 'admin_id' => auth()->id()]
        );

        return back()->with('success', "User {$user->name} status changed to {$newStatus}.");
    }
    public function trainerDetail(User $user): View
    {
        if ($user->role !== 'trainer') {
            abort(404, 'Trainer not found');
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

    }

