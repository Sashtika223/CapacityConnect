<?php

namespace App\Policies;

use App\Models\PracticalAssessment;
use App\Models\User;

class PracticalAssessmentPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function view(User $user, PracticalAssessment $practical): bool
    {
        if ($user->isTrainer()) {
            return $practical->course->trainer_id === $user->id;
        }
        if ($user->isTrainee()) {
            return $user->enrollments()->where('course_id', $practical->course_id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTrainer() && $user->isActive();
    }

    public function update(User $user, PracticalAssessment $practical): bool
    {
        return $user->isTrainer() && $practical->course->trainer_id === $user->id;
    }

    public function delete(User $user, PracticalAssessment $practical): bool
    {
        return $user->isTrainer() && $practical->course->trainer_id === $user->id
            && $practical->submissions()->count() === 0;
    }

    /**
     * Trainers may only grade submissions for their own course's practicals.
     */
    public function grade(User $user, PracticalAssessment $practical): bool
    {
        return $user->isTrainer() && $practical->course->trainer_id === $user->id;
    }
}
