<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Content that depends on the CSV catalogue import.
 *
 * Wire this into `composer setup` (or run manually) after `academia:import`:
 *
 *   php artisan db:seed --class=PostImportSeeder
 */
class PostImportSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SiteCompletenessSeeder::class,
            SeoSeeder::class,
        ]);
    }
}
