<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    /**
     * Admins can do anything. Trainers can only manage assessments on their own courses.
     */
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, Assessment $assessment): bool
    {
        if ($user->isTrainer()) {
            return $assessment->course->trainer_id === $user->id;
        }
        // Trainees can view assessments for courses they are enrolled in
        if ($user->isTrainee()) {
            return $user->enrollments()->where('course_id', $assessment->course_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTrainer() && $user->isActive();
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $user->isTrainer() && $assessment->course->trainer_id === $user->id;
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->isTrainer() && $assessment->course->trainer_id === $user->id
            && $assessment->attempts()->count() === 0;
    }

    /**
     * A trainee can attempt an assessment only if enrolled in the course and within attempt limits.
     */
    public function attempt(User $user, Assessment $assessment): bool
    {
        if (! $user->isTrainee()) {
            return false;
        }
        $enrolled = $user->enrollments()->where('course_id', $assessment->course_id)->exists();
        if (! $enrolled) {
            return false;
        }
        $attempts = $user->assessmentAttempts()->where('assessment_id', $assessment->id)->count();

        return $attempts < $assessment->max_attempts;
    }
}
