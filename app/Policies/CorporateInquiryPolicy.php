<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Leads\Models\CorporateInquiry;
use App\Models\User;

class CorporateInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'sales-manager']);
    }

    /**
     * Sales sees their own leads plus the unassigned queue.
     *
     * Not every lead: a shared pipeline where everyone reads everyone's
     * negotiation notes is a pipeline nobody writes honest notes in.
     */
    public function view(User $user, CorporateInquiry $inquiry): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if (! $user->hasRole('sales-manager')) {
            return false;
        }

        return $inquiry->assigned_to === $user->id || $inquiry->assigned_to === null;
    }

    public function update(User $user, CorporateInquiry $inquiry): bool
    {
        return $this->view($user, $inquiry);
    }

    public function assign(User $user): bool
    {
        return $user->can('assign_inquiry');
    }

    /** Exporting personal data is separately permissioned and always logged. */
    public function export(User $user): bool
    {
        return $user->can('export_personal_data');
    }

    public function delete(User $user, CorporateInquiry $inquiry): bool
    {
        return $user->hasRole('admin');
    }
}
