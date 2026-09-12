<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CourseSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_course_schedule');
    }

    public function view(User $user): bool
    {
        return $user->can('view_course_schedule');
    }

    public function create(User $user): bool
    {
        return $user->can('create_course_schedule');
    }

    public function update(User $user): bool
    {
        return $user->can('update_course_schedule');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_course_schedule');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_course_schedule');
    }

    public function cancel(User $user): bool
    {
        return $user->can('cancel_course_schedule');
    }
}
