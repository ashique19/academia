<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_city');
    }

    public function view(User $user): bool
    {
        return $user->can('view_city');
    }

    public function create(User $user): bool
    {
        return $user->can('create_city');
    }

    public function update(User $user): bool
    {
        return $user->can('update_city');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_city');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_city');
    }
}
