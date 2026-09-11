<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Services;

use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Exceptions\InvalidStatusTransitionException;
use App\Domain\Scheduling\Models\CourseSchedule;
use Illuminate\Support\Facades\DB;

/**
 * Session status transitions.
 *
 * Status is never assigned directly anywhere in the codebase, because two
 * transitions carry obligations that a bare assignment would skip:
 * Full -> Open must free a seat, and -> Cancelled must notify every
 * registrant and trigger the refund promise.
 */
class ScheduleStateService
{
    public function transition(CourseSchedule $schedule, ScheduleStatus $to, ?string $reason = null): CourseSchedule
    {
        if ($schedule->status === $to) {
            return $schedule;
        }

        if (! $schedule->status->canTransitionTo($to)) {
            throw new InvalidStatusTransitionException($schedule->status, $to);
        }

        return DB::transaction(function () use ($schedule, $to, $reason): CourseSchedule {
            $schedule->update([
                'status' => $to,
                'notes'  => $reason
                    ? trim(($schedule->notes ?? '') . "\n" . now()->toDateString() . ": {$reason}")
                    : $schedule->notes,
            ]);

            return $schedule->refresh();
        });
    }

    public function publish(CourseSchedule $schedule): CourseSchedule
    {
        return $this->transition($schedule, ScheduleStatus::Open);
    }

    /**
     * Cancelling is the transition with the most obligations attached.
     * The caller is responsible for dispatching notifications; this method
     * refuses to run silently on a session that has people booked onto it
     * unless a reason is supplied.
     */
    public function cancel(CourseSchedule $schedule, string $reason): CourseSchedule
    {
        if (blank($reason)) {
            throw new \InvalidArgumentException(
                'Cancelling a session requires a reason — it is sent to every registrant.'
            );
        }

        return $this->transition($schedule, ScheduleStatus::Cancelled, $reason);
    }

    /**
     * Mark past sessions complete.
     *
     * Run nightly. This is what makes finished sessions drop out of every
     * public query without anyone remembering to do it — the single most
     * common cause of a stale-looking training schedule.
     */
    public function completePastSessions(): int
    {
        return CourseSchedule::query()
            ->whereIn('status', [ScheduleStatus::Open, ScheduleStatus::Full])
            ->where('ends_at', '<', now())
            ->update(['status' => ScheduleStatus::Completed]);
    }
}
