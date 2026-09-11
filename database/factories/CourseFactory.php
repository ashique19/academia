<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalogue\Enums\CourseLevel;
use App\Domain\Catalogue\Enums\CourseStatus;
use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Course> */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = Str::title($this->faker->unique()->words(4, true));

        return [
            'course_subcategory_id' => CourseSubcategory::factory(),
            'code' => 'ACA-'.$this->faker->unique()->numberBetween(1000, 9999),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => $this->faker->sentence(14),
            'description' => $this->faker->paragraphs(3, true),
            'learning_objectives' => $this->faker->sentences(4),
            'target_audience' => $this->faker->sentence(),
            'duration_days' => $this->faker->randomElement([0.5, 1, 2, 3, 5]),
            'level' => $this->faker->randomElement(CourseLevel::cases()),
            'max_participants' => 14,
            // Money is always an integer number of cents.
            'price_cents' => $this->faker->numberBetween(500, 4000) * 100,
            'currency' => 'EUR',
            'status' => CourseStatus::Published,
            'published_at' => now()->subDays($this->faker->numberBetween(1, 200)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => CourseStatus::Draft, 'published_at' => null]);
    }

    public function requiringQuote(): static
    {
        return $this->state(fn () => ['price_cents' => null]);
    }
}
