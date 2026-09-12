<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_course');
    }

    public function view(User $user): bool
    {
        return $user->can('view_course');
    }

    public function create(User $user): bool
    {
        return $user->can('create_course');
    }

    public function update(User $user): bool
    {
        return $user->can('update_course');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_course');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_course');
    }

    public function publish(User $user): bool
    {
        return $user->can('publish_course');
    }
}
