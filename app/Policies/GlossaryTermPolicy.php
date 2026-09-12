<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class GlossaryTermPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_glossary_term');
    }

    public function view(User $user): bool
    {
        return $user->can('view_glossary_term');
    }

    public function create(User $user): bool
    {
        return $user->can('create_glossary_term');
    }

    public function update(User $user): bool
    {
        return $user->can('update_glossary_term');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_glossary_term');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_glossary_term');
    }
}
