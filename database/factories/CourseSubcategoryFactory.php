<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\CourseSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<CourseSubcategory> */
class CourseSubcategoryFactory extends Factory
{
    protected $model = CourseSubcategory::class;

    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(2, true));

        return [
            'course_category_id' => CourseCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
        ];
    }
}
