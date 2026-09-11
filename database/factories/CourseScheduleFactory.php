<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CourseSchedule> */
class CourseScheduleFactory extends Factory
{
    protected $model = CourseSchedule::class;

    /**
     * Keep ends_at consistent with starts_at.
     *
     * Callers routinely override only starts_at (e.g. now()->addMonth()); left
     * alone, ends_at would keep the definition's unrelated date and could land
     * before starts_at, which the database now rejects. Recompute it whenever
     * the pair is inconsistent, without disturbing states that set both.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (CourseSchedule $schedule): void {
            if ($schedule->ends_at === null || $schedule->ends_at <= $schedule->starts_at) {
                $schedule->ends_at = $schedule->starts_at->copy()->addHours(8);
            }
        });
    }

    public function definition(): array
    {
        $startsAt = now()->addDays($this->faker->numberBetween(7, 180))->setTime(9, 0);

        return [
            'reference' => 'S'.$this->faker->unique()->numberBetween(10000, 99999),
            'course_id' => Course::factory(),
            'delivery_mode_id' => DeliveryMode::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->copy()->addHours(8),
            'timezone' => 'Europe/Amsterdam',
            'seat_limit' => 14,
            'seats_taken' => 0,
            'price_cents' => $this->faker->numberBetween(500, 3000) * 100,
            'currency' => 'EUR',
            'status' => ScheduleStatus::Open,
            'language' => 'en',
        ];
    }

    public function inCity(?City $city = null): static
    {
        return $this->state(fn () => [
            'city_id' => $city?->id ?? City::factory(),
            'country_id' => $city?->country_id,
        ]);
    }

    public function full(): static
    {
        return $this->state(fn (array $attributes) => [
            'seats_taken' => $attributes['seat_limit'] ?? 14,
            'status' => ScheduleStatus::Full,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subDays(30),
            'ends_at' => now()->subDays(30)->addHours(8),
        ]);
    }
}
