<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalogue\Models\CourseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<CourseCategory> */
class CourseCategoryFactory extends Factory
{
    protected $model = CourseCategory::class;

    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(2, true));

        return [
            'name'       => $name,
            'slug'       => Str::slug($name),
            'summary'    => $this->faker->sentence(12),
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_active'  => true,
        ];
    }
}
