<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class RegistrationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_registration');
    }

    public function view(User $user): bool
    {
        return $user->can('view_registration');
    }

    public function create(User $user): bool
    {
        return $user->can('create_registration');
    }

    public function update(User $user): bool
    {
        return $user->can('update_registration');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_registration');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_registration');
    }
}
