<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Services;

use App\Domain\Leads\Enums\RegistrationStatus;
use App\Domain\Leads\Models\Registration;
use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Exceptions\InsufficientSeatsException;
use App\Domain\Scheduling\Exceptions\SessionNotBookableException;
use App\Domain\Scheduling\Models\CourseSchedule;
use Illuminate\Support\Facades\DB;

/**
 * Seat reservation.
 *
 * ---------------------------------------------------------------------------
 * THIS IS THE PART OF A TRAINING PLATFORM THAT ACTUALLY BREAKS.
 *
 * Two people click "register" on the last seat within the same second. Naive
 * code reads seats_available = 1 twice and writes two registrations. You have
 * oversold a classroom, and one of them travels to Amsterdam for nothing.
 *
 * The fix is pessimistic locking inside a transaction: lockForUpdate() issues
 * SELECT ... FOR UPDATE, which blocks any concurrent transaction touching the
 * same row until this one commits. The second request then re-reads the
 * updated count and fails cleanly.
 *
 * A CHECK constraint (seats_taken <= seat_limit) backs this at the database
 * level, so a future code path that bypasses this service still cannot
 * oversell — it errors instead.
 *
 * MVP nuance: registration is interest, not paid booking. The locking is
 * built now anyway, because retrofitting it after payments land means
 * auditing every write path that already exists.
 * ---------------------------------------------------------------------------
 */
class RegistrationService
{
    /**
     * @throws SessionNotBookableException
     * @throws InsufficientSeatsException
     */
    public function reserve(CourseSchedule $schedule, array $attributes, int $seats = 1): Registration
    {
        return DB::transaction(function () use ($schedule, $attributes, $seats): Registration {

            // Re-read under a row lock. Never trust the instance passed in —
            // it was loaded before the request and may already be stale.
            /** @var CourseSchedule $locked */
            $locked = CourseSchedule::query()
                ->whereKey($schedule->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $locked->status->isBookable() || $locked->starts_at->isPast()) {
                throw new SessionNotBookableException($locked);
            }

            $available = $locked->seat_limit - $locked->seats_taken;

            if ($available < $seats) {
                throw new InsufficientSeatsException($locked, $seats, max(0, $available));
            }

            $locked->increment('seats_taken', $seats);
            $locked->refresh();

            if ($locked->seats_taken >= $locked->seat_limit) {
                $locked->update(['status' => ScheduleStatus::Full]);
            }

            $registration = $locked->registrations()->create([
                ...$attributes,
                'seats'  => $seats,
                'status' => $attributes['status'] ?? RegistrationStatus::Interest,
            ]);

            $locked->course()->increment('booking_count', $seats);

            return $registration;
        });
    }

    /**
     * Release seats and reopen the session if it had filled.
     *
     * Symmetrical with reserve(): same lock, same transaction. A cancellation
     * that frees a seat without reopening the session leaves a bookable date
     * showing as Full, which is silent lost revenue.
     */
    public function cancel(Registration $registration, ?string $reason = null): Registration
    {
        return DB::transaction(function () use ($registration, $reason): Registration {

            /** @var CourseSchedule $locked */
            $locked = CourseSchedule::query()
                ->whereKey($registration->course_schedule_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $registration->status->holdsSeat()) {
                return $registration;   // idempotent — cancelling twice is a no-op
            }

            $locked->decrement('seats_taken', $registration->seats);
            $locked->refresh();

            if ($locked->status === ScheduleStatus::Full && $locked->seats_taken < $locked->seat_limit) {
                $locked->update(['status' => ScheduleStatus::Open]);
            }

            $registration->update([
                'status'  => RegistrationStatus::Cancelled,
                'message' => $reason ? trim(($registration->message ?? '') . "\n\nCancelled: " . $reason) : $registration->message,
            ]);

            return $registration->refresh();
        });
    }

    /**
     * Recount seats from the registration rows.
     *
     * The counter is authoritative in normal operation; this is the repair
     * tool for when it is not — after a bad import, a restored backup, or a
     * bug. Run by the nightly maintenance command.
     */
    public function recount(CourseSchedule $schedule): int
    {
        return DB::transaction(function () use ($schedule): int {
            /** @var CourseSchedule $locked */
            $locked = CourseSchedule::query()
                ->whereKey($schedule->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $actual = (int) $locked->registrations()->holdingSeat()->sum('seats');

            if ($actual !== $locked->seats_taken) {
                $locked->update(['seats_taken' => $actual]);
            }

            return $actual;
        });
    }
}
