<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Course $course): bool
    {
        if ($course->isPublished()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $user->isAdmin() || ($user->isTrainer() && $course->trainer_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || ($user->isTrainer() && $user->isActive());
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isAdmin() || ($user->isTrainer() && $course->trainer_id === $user->id);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isAdmin() || ($user->isTrainer() && $course->trainer_id === $user->id && $course->enrollments()->count() === 0);
    }

    public function approve(User $user): bool
    {
        return $user->isAdmin();
    }
}
