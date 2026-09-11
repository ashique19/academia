<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Scheduling\Models\CourseSchedule;
use App\Models\User;

class CourseSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'course-manager', 'sales-manager', 'trainer']);
    }

    public function view(User $user, CourseSchedule $schedule): bool
    {
        if ($user->hasAnyRole(['admin', 'course-manager', 'sales-manager'])) {
            return true;
        }

        return $user->trainer_id !== null && $user->trainer_id === $schedule->trainer_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'course-manager']);
    }

    public function update(User $user, CourseSchedule $schedule): bool
    {
        return $user->hasAnyRole(['admin', 'course-manager']);
    }

    public function cancel(User $user, CourseSchedule $schedule): bool
    {
        return $user->can('cancel_course_schedule');
    }

    /**
     * Participant lists are personal data.
     *
     * A trainer sees them only for their OWN sessions, and only from seven
     * days before the start date. There is no operational reason to hold a
     * list of names months in advance, and GDPR data minimisation says do
     * not, so the window is enforced rather than documented.
     */
    public function viewParticipants(User $user, CourseSchedule $schedule): bool
    {
        if ($user->hasAnyRole(['admin', 'course-manager'])) {
            return true;
        }

        if ($user->trainer_id === null || $user->trainer_id !== $schedule->trainer_id) {
            return false;
        }

        return $schedule->starts_at->isBefore(now()->addDays(7))
            && $schedule->ends_at->isAfter(now()->subDays(30));
    }
}
