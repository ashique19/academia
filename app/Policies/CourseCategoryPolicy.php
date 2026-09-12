<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CourseCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_course_category');
    }

    public function view(User $user): bool
    {
        return $user->can('view_course_category');
    }

    public function create(User $user): bool
    {
        return $user->can('create_course_category');
    }

    public function update(User $user): bool
    {
        return $user->can('update_course_category');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_course_category');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_course_category');
    }
}
