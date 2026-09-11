<?php

declare(strict_types=1);

use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Exceptions\InsufficientSeatsException;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Scheduling\Services\RegistrationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
 * Seat integrity.
 *
 * This is the part of a training platform that actually breaks: two people
 * click "register" on the last seat within the same second, naive code reads
 * seats_available = 1 twice, and a classroom is oversold.
 */
beforeEach(function () {
    $this->service = new RegistrationService;
});

it('decrements available seats when a place is reserved', function () {
    $schedule = CourseSchedule::factory()->create([
        'seat_limit' => 10,
        'seats_taken' => 0,
        'status' => ScheduleStatus::Open,
        'starts_at' => now()->addMonth(),
    ]);

    $this->service->reserve($schedule, ['name' => 'Test', 'email' => 'test@example.com']);

    expect($schedule->fresh()->seats_taken)->toBe(1)
        ->and($schedule->fresh()->seats_available)->toBe(9);
});

it('marks a session Full on the last seat', function () {
    $schedule = CourseSchedule::factory()->create([
        'seat_limit' => 1, 'seats_taken' => 0,
        'status' => ScheduleStatus::Open, 'starts_at' => now()->addMonth(),
    ]);

    $this->service->reserve($schedule, ['name' => 'Test', 'email' => 'test@example.com']);

    expect($schedule->fresh()->status)->toBe(ScheduleStatus::Full);
});

it('refuses to oversell', function () {
    $schedule = CourseSchedule::factory()->create([
        'seat_limit' => 1, 'seats_taken' => 1,
        'status' => ScheduleStatus::Open, 'starts_at' => now()->addMonth(),
    ]);

    $this->service->reserve($schedule, ['name' => 'Test', 'email' => 'test@example.com']);
})->throws(InsufficientSeatsException::class);

it('frees the seat and reopens the session on cancellation', function () {
    $schedule = CourseSchedule::factory()->create([
        'seat_limit' => 1, 'seats_taken' => 0,
        'status' => ScheduleStatus::Open, 'starts_at' => now()->addMonth(),
    ]);

    $registration = $this->service->reserve($schedule, ['name' => 'Test', 'email' => 'test@example.com']);
    expect($schedule->fresh()->status)->toBe(ScheduleStatus::Full);

    $this->service->cancel($registration);

    expect($schedule->fresh()->status)->toBe(ScheduleStatus::Open)
        ->and($schedule->fresh()->seats_taken)->toBe(0);
});

it('rejects an oversold write at the database level, bypassing the service', function () {
    $schedule = CourseSchedule::factory()->create([
        'seat_limit' => 5, 'seats_taken' => 0,
        'status' => ScheduleStatus::Open, 'starts_at' => now()->addMonth(),
    ]);

    // A future code path that skips RegistrationService must still be unable
    // to oversell — the CHECK constraint (Postgres/MySQL) or trigger (SQLite)
    // is the backstop behind the pessimistic lock.
    expect(fn () => DB::table('course_schedules')
        ->where('id', $schedule->id)
        ->update(['seats_taken' => $schedule->seat_limit + 1]))
        ->toThrow(QueryException::class);
});

it('is idempotent when the same registration is cancelled twice', function () {
    $schedule = CourseSchedule::factory()->create([
        'seat_limit' => 5, 'seats_taken' => 0,
        'status' => ScheduleStatus::Open, 'starts_at' => now()->addMonth(),
    ]);

    $registration = $this->service->reserve($schedule, ['name' => 'Test', 'email' => 'test@example.com']);

    $this->service->cancel($registration);
    $this->service->cancel($registration->fresh());

    expect($schedule->fresh()->seats_taken)->toBe(0);
});
