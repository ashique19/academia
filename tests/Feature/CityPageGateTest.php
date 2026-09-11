<?php

declare(strict_types=1);

use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Shared\Models\City;

/**
 * The doorway-page gate.
 *
 * A city page must 404 unless a real, scheduled, bookable session exists.
 * That single check is the difference between a local landing page and a
 * doorway page, and doorway pages are a manual-action category — so it is
 * tested rather than merely documented.
 */
it('404s for a city with no upcoming sessions', function () {
    $city = City::factory()->create();

    $this->get(route('locations.city', [$city->country, $city]))->assertNotFound();
});

it('renders for a city with a real bookable session', function () {
    $city = City::factory()->hasSchedules(1, [
        'status' => ScheduleStatus::Open,
        'starts_at' => now()->addMonth(),
    ])->create();

    $this->get(route('locations.city', [$city->country, $city]))->assertOk();
});

it('404s when the only session has already passed', function () {
    $city = City::factory()->hasSchedules(1, [
        'status' => ScheduleStatus::Open,
        'starts_at' => now()->subMonth(),
    ])->create();

    $this->get(route('locations.city', [$city->country, $city]))->assertNotFound();
});
