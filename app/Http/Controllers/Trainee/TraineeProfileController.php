<?php

namespace App\Http\Controllers\Trainee;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Experience;
use App\Models\Profile;
use App\Models\Qualification;
use App\Models\Skill;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TraineeProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user()->load([
            'profile',
            'department',
            'qualifications',
            'experiences',
            'skills',
            'userCompetencies.competency',
            'certificates.course',
            'enrollments.course',
            'assessmentAttempts.assessment.course',
        ]);

        $departments = Department::orderBy('name')->get();
        $availableSkills = Skill::orderBy('name')->get();

        // Profile completion
        $completionPct = $user->profile->completionPercentage($user);

        // Training history
        $completedEnrollments = $user->enrollments->where('status', 'completed');
        $activeEnrollments = $user->enrollments->whereIn('status', ['enrolled', 'in_progress']);

        // Recent assessment performance
        $recentAttempts = $user->assessmentAttempts->sortByDesc('created_at')->take(5);

        return view('trainee.profile.show', compact(
            'user',
            'departments',
            'availableSkills',
            'completionPct',
            'completedEnrollments',
            'activeEnrollments',
            'recentAttempts',
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'nullable|exists:departments,id',
            'job_title' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'emergency_unit' => 'nullable|string|max:255',
            'areas_of_expertise' => 'nullable|string|max:500',
        ]);

        $user->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'department_id' => $request->input('department_id'),
        ]);

        $profileData = [
            'job_title' => $request->input('job_title'),
            'designation' => $request->input('designation'),
            'location' => $request->input('location'),
            'bio' => $request->input('bio'),
            'emergency_unit' => $request->input('emergency_unit'),
            'areas_of_expertise' => $request->input('areas_of_expertise'),
        ];

        $profile = $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        // Auto-assign employee ID if not set
        if (! $profile->employee_id) {
            $profile->update(['employee_id' => Profile::generateEmployeeId()]);
        }

        AuditLogService::log('profile.updated', $user);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function uploadAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $user = Auth::user();
        $profile = $user->profile;

        // Delete old avatar if exists
        if ($profile->avatar_path) {
            Storage::delete($profile->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $profile->update(['avatar_path' => $path]);

        AuditLogService::log('profile.avatar_updated', $user);

        return back()->with('success', 'Profile photo updated!');
    }

    public function addQualification(Request $request): RedirectResponse
    {
        $request->validate([
            'degree' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'field_of_study' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|digits:4',
        ]);

        Qualification::create([
            'user_id' => Auth::id(),
            'degree' => $request->input('degree'),
            'institution' => $request->input('institution'),
            'field_of_study' => $request->input('field_of_study'),
            'graduation_year' => $request->input('graduation_year'),
        ]);

        return back()->with('success', 'Qualification added.');
    }

    public function addExperience(Request $request): RedirectResponse
    {
        $request->validate([
            'position' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        Experience::create([
            'user_id' => Auth::id(),
            'position' => $request->input('position'),
            'organization' => $request->input('organization'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'is_current' => $request->boolean('is_current'),
            'description' => $request->input('description'),
        ]);

        return back()->with('success', 'Work experience record added.');
    }

    public function addSkill(Request $request): RedirectResponse
    {
        $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'proficiency' => 'required|in:beginner,intermediate,advanced,expert',
        ]);

        $user = Auth::user();
        $user->skills()->syncWithoutDetaching([
            $request->input('skill_id') => [
                'proficiency' => $request->input('proficiency'),
                'verified_at' => now(),
            ],
        ]);

        AuditLogService::log('profile.skill_added', $user);

        return back()->with('success', 'Skill added to your digital profile!');
    }

    public function addInterest(Request $request): RedirectResponse
    {
        $request->validate([
            'interest' => 'required|string|max:100',
        ]);

        $user = Auth::user();
        $profile = $user->profile;
        $interests = $profile->interests ?? [];

        if (! in_array($request->input('interest'), $interests)) {
            $interests[] = trim($request->input('interest'));
            $profile->update(['interests' => $interests]);
        }

        return back()->with('success', 'Interest topic added.');
    }

    public function removeInterest(Request $request): RedirectResponse
    {
        $request->validate(['interest' => 'required|string']);

        $user = Auth::user();
        $profile = $user->profile;
        $interests = array_values(array_filter(
            $profile->interests ?? [],
            fn ($i) => $i !== $request->input('interest')
        ));
        $profile->update(['interests' => $interests]);

        return back()->with('success', 'Interest removed.');
    }

    public function toggleTwoFactor(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;
        $currentState = $profile->two_factor_enabled ?? false;
        $profile->update(['two_factor_enabled' => ! $currentState]);

        $statusStr = ! $currentState ? 'enabled' : 'disabled';

        return back()->with('success', "Two-Factor Authentication has been {$statusStr}.");
    }

    public function unenroll(Request $request, $courseId): RedirectResponse
    {
        $user = Auth::user();
        $enrollment = $user->enrollments()
            ->where('course_id', $courseId)
            ->whereIn('status', ['enrolled', 'in_progress'])
            ->first();

        if (! $enrollment) {
            return back()->with('error', 'You are not enrolled in this course or it cannot be unenrolled.');
        }

        // Prevent unenrolling from completed courses
        if ($enrollment->status === 'completed') {
            return back()->with('error', 'You cannot unenroll from a completed course.');
        }

        $enrollment->update([
            'status' => 'failed',
            'unenrolled_at' => now(),
        ]);

        AuditLogService::log('course.unenrolled', $enrollment);

        return redirect()->route('trainee.learning.index')
            ->with('info', 'You have been unenrolled from the course.');
    }
}
