<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<City> */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->city();

        return [
            'country_id' => Country::factory(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->randomNumber(4),
            'intro' => $this->faker->paragraph(),
            'latitude' => $this->faker->latitude(35, 60),
            'longitude' => $this->faker->longitude(-10, 25),
            'is_active' => true,
        ];
    }
}
