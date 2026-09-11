<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Content\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'content-editor', 'course-manager', 'sales-manager']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'content-editor']);
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $user->hasAnyRole(['admin', 'content-editor']);
    }

    /**
     * Verification is deliberately NOT available to content editors.
     *
     * Verification is the gate that makes published social proof meaningful.
     * If the person under pressure to publish can also self-verify, the gate
     * is decorative — so it sits with admins, who are not measured on
     * publishing volume.
     */
    public function verify(User $user, Testimonial $testimonial): bool
    {
        return $user->can('verify_testimonial');
    }

    public function delete(User $user, Testimonial $testimonial): bool
    {
        return $user->hasRole('admin');
    }
}
