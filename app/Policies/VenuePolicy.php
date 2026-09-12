<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class VenuePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_venue');
    }

    public function view(User $user): bool
    {
        return $user->can('view_venue');
    }

    public function create(User $user): bool
    {
        return $user->can('create_venue');
    }

    public function update(User $user): bool
    {
        return $user->can('update_venue');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_venue');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_venue');
    }
}
