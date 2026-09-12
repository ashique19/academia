<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CaseStudyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_case_study');
    }

    public function view(User $user): bool
    {
        return $user->can('view_case_study');
    }

    public function create(User $user): bool
    {
        return $user->can('create_case_study');
    }

    public function update(User $user): bool
    {
        return $user->can('update_case_study');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_case_study');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_case_study');
    }
}
