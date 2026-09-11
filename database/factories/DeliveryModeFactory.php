<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalogue\Models\DeliveryMode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<DeliveryMode> */
class DeliveryModeFactory extends Factory
{
    protected $model = DeliveryMode::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement(['Online', 'Classroom', 'Onsite', 'Self-paced']);

        return ['name' => $name, 'slug' => Str::slug($name), 'sort_order' => 0];
    }
}
