<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Shared\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Country> */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->country();

        return [
            'name'      => $name,
            'iso2'      => strtoupper($this->faker->unique()->lexify('??')),
            'slug'      => Str::slug($name) . '-' . $this->faker->unique()->randomNumber(4),
            'currency'  => 'EUR',
            'timezone'  => 'Europe/Amsterdam',
            'is_active' => true,
        ];
    }
}
