<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class DeliveryModePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_delivery_mode');
    }

    public function view(User $user): bool
    {
        return $user->can('view_delivery_mode');
    }

    public function create(User $user): bool
    {
        return $user->can('create_delivery_mode');
    }

    public function update(User $user): bool
    {
        return $user->can('update_delivery_mode');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_delivery_mode');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_delivery_mode');
    }
}
