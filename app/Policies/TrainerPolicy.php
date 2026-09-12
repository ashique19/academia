<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class TrainerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_trainer');
    }

    public function view(User $user): bool
    {
        return $user->can('view_trainer');
    }

    public function create(User $user): bool
    {
        return $user->can('create_trainer');
    }

    public function update(User $user): bool
    {
        return $user->can('update_trainer');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_trainer');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_trainer');
    }
}
