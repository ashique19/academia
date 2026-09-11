<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Catalogue\Models\Course;
use Illuminate\Console\Command;

/**
 * Records the EU Omnibus reference price.
 *
 * Directive (EU) 2019/2161 requires a price reduction to be announced against
 * the lowest price applied in the previous 30 days — not against whatever the
 * list price says on the day the campaign starts.
 *
 * Running this daily means the evidence exists BEFORE it is needed, rather
 * than being reconstructed afterwards if a consumer authority asks.
 */
class RecordPriorPrices extends Command
{
    protected $signature = 'academia:record-prior-prices {--force : Overwrite existing reference prices}';

    protected $description = 'Record the reference price a discount must be announced against.';

    public function handle(): int
    {
        $query = Course::query()->whereNotNull('price_cents');

        if (! $this->option('force')) {
            $query->whereNull('prior_price_cents');
        }

        $recorded = 0;

        $query->chunkById(200, function ($courses) use (&$recorded): void {
            foreach ($courses as $course) {
                // Keep the LOWEST figure seen. If the list price has fallen
                // since the last run, the lower one is the compliant
                // reference — never the higher, more flattering one.
                $reference = $course->prior_price_cents === null
                    ? $course->price_cents
                    : min($course->prior_price_cents, $course->price_cents);

                $course->updateQuietly(['prior_price_cents' => $reference]);
                $recorded++;
            }
        });

        $this->info("Recorded reference prices for {$recorded} course(s).");

        return self::SUCCESS;
    }
}
