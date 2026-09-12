<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_setting');
    }

    public function view(User $user): bool
    {
        return $user->can('view_setting');
    }

    public function create(User $user): bool
    {
        return $user->can('create_setting');
    }

    public function update(User $user): bool
    {
        return $user->can('update_setting');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_setting');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_setting');
    }
}
