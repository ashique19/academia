<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        // Note both `reason` and `ends_at` are supplied. They are NOT NULL
        // columns precisely so that a campaign without a stated reason or an
        // end date cannot be created — rule 1 of the promotion policy is a
        // schema constraint, not a guideline.
        Promotion::updateOrCreate(
            ['code' => 'AUTUMN20'],
            [
                'name'       => 'Autumn Skills Sprint',
                'percentage' => 20,
                'type'       => 'campaign',
                'reason'     => 'Autumn is when training budgets are committed for the following year, '
                              . 'and it is the quietest month in our classrooms. Filling those seats is '
                              . 'worth more to us than the margin.',
                'starts_at'  => now()->startOfMonth(),
                'ends_at'    => now()->addMonth()->endOfMonth(),
                'blurb'      => 'Every technology and finance course, booked this month, for any date up to March 2027.',
                'applies_to_category_slugs' => ['it-and-cybersecurity', 'finance-and-accounting', 'data-analytics-and-bi'],
                'is_active'  => true,
            ]
        );
    }
}
