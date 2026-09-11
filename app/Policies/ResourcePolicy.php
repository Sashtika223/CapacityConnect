<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    public function download(User $user, Resource $resource): bool
    {
        if (! $resource->is_private) {
            return true;
        }

        if ($user->isAdmin() || $resource->uploader_id === $user->id) {
            return true;
        }

        // Trainee must be actively enrolled in the course
        return $user->enrollments()->where('course_id', $resource->course_id)->exists();
    }
}
