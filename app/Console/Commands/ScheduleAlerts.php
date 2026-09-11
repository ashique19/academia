<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use Illuminate\Console\Command;

/**
 * Staleness alerting.
 *
 * An out-of-date training schedule is the worst trust signal in this category
 * — worse than no schedule at all, because it says the business is not
 * running. Fifteen lines of checking prevents the slow decay that kills these
 * sites, and it fires before a buyer notices rather than after.
 */
class ScheduleAlerts extends Command
{
    protected $signature = 'academia:schedule-alerts';

    protected $description = 'Warn when the published training schedule is going stale.';

    public function handle(): int
    {
        $config  = config('academia.schedule.alerts');
        $alerts  = [];

        foreach (CourseCategory::active()->get() as $category) {
            $count = CourseSchedule::query()
                ->upcoming()->bookable()
                ->whereHas('course.subcategory', fn ($q) => $q->where('course_category_id', $category->id))
                ->count();

            if ($count < $config['min_sessions_per_category']) {
                $alerts[] = "{$category->name}: only {$count} upcoming session(s).";
            }
        }

        $emptyCities = City::query()
            ->active()
            ->whereDoesntHave('schedules', fn ($q) => $q->bookable()
                ->whereBetween('starts_at', [now(), now()->addDays($config['city_lookahead_days'])]))
            ->pluck('name');

        if ($emptyCities->isNotEmpty()) {
            $alerts[] = sprintf(
                '%d city page(s) will 404 within %d days: %s',
                $emptyCities->count(),
                $config['city_lookahead_days'],
                $emptyCities->take(8)->implode(', ')
            );
        }

        $farHorizon = CourseSchedule::query()
            ->bookable()
            ->where('starts_at', '>', now()->addDays(90))
            ->count();

        if ($farHorizon < $config['min_sessions_beyond_90d']) {
            $alerts[] = "Only {$farHorizon} session(s) scheduled beyond 90 days — "
                . 'buyers planning next quarter have nothing to book.';
        }

        if ($alerts === []) {
            $this->info('Schedule is healthy.');

            return self::SUCCESS;
        }

        $this->warn('Schedule needs attention:');

        foreach ($alerts as $alert) {
            $this->line('  • ' . $alert);
        }

        // Wired to a notification once the mail stack is configured; the
        // console output is what the scheduler captures in the meantime.
        return self::SUCCESS;
    }
}
