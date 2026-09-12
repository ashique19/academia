<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CertificationSchemePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_certification_scheme');
    }

    public function view(User $user): bool
    {
        return $user->can('view_certification_scheme');
    }

    public function create(User $user): bool
    {
        return $user->can('create_certification_scheme');
    }

    public function update(User $user): bool
    {
        return $user->can('update_certification_scheme');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_certification_scheme');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_certification_scheme');
    }
}
