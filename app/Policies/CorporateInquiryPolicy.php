<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class CorporateInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_corporate_inquiry');
    }

    public function view(User $user): bool
    {
        return $user->can('view_corporate_inquiry');
    }

    public function create(User $user): bool
    {
        return $user->can('create_corporate_inquiry');
    }

    public function update(User $user): bool
    {
        return $user->can('update_corporate_inquiry');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_corporate_inquiry');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_corporate_inquiry');
    }

    public function restore(User $user): bool
    {
        return $user->can('delete_corporate_inquiry');
    }

    public function forceDelete(User $user): bool
    {
        return $user->can('delete_corporate_inquiry');
    }

    public function assign(User $user): bool
    {
        return $user->can('assign_inquiry');
    }

    public function exportPersonalData(User $user): bool
    {
        return $user->can('export_personal_data');
    }
}
