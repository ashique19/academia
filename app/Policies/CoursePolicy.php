<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Catalogue\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessAdmin();
    }

    public function view(User $user, Course $course): bool
    {
        return $user->canAccessAdmin();
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'course-manager']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['admin', 'course-manager']);
    }

    /** Publishing is separate from saving — a draft edit is not a release. */
    public function publish(User $user, Course $course): bool
    {
        return $user->can('publish_course');
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['admin', 'course-manager']);
    }
}
