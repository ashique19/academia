<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class PromotionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_promotion');
    }

    public function view(User $user): bool
    {
        return $user->can('view_promotion');
    }

    public function create(User $user): bool
    {
        return $user->can('create_promotion');
    }

    public function update(User $user): bool
    {
        return $user->can('update_promotion');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_promotion');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_promotion');
    }
}
