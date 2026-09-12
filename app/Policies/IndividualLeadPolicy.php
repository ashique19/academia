<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class IndividualLeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_individual_lead');
    }

    public function view(User $user): bool
    {
        return $user->can('view_individual_lead');
    }

    public function create(User $user): bool
    {
        return $user->can('create_individual_lead');
    }

    public function update(User $user): bool
    {
        return $user->can('update_individual_lead');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_individual_lead');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_individual_lead');
    }
}
