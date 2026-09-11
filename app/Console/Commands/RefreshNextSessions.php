<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Scheduling\Enums\ScheduleStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Safety net behind CourseScheduleObserver.
 *
 * The observer keeps courses.next_session_at correct on every ordinary write,
 * but an observer never sees a raw query, a bulk update or a restored backup.
 * Recomputing the whole column nightly costs almost nothing and means the
 * catalogue's "starting soonest" sort cannot quietly drift out of truth.
 */
class RefreshNextSessions extends Command
{
    protected $signature = 'academia:refresh-next-sessions';

    protected $description = 'Recompute the denormalised courses.next_session_at column.';

    public function handle(): int
    {
        $soonest = DB::table('course_schedules')
            ->select('course_id', DB::raw('MIN(starts_at) as next_start'))
            ->where('status', ScheduleStatus::Open->value)
            ->where('starts_at', '>', now())
            ->whereNull('deleted_at')
            ->groupBy('course_id')
            ->pluck('next_start', 'course_id');

        DB::table('courses')->update(['next_session_at' => null]);

        $updated = 0;

        foreach ($soonest as $courseId => $nextStart) {
            $updated += DB::table('courses')
                ->where('id', $courseId)
                ->update(['next_session_at' => $nextStart]);
        }

        $this->info("Refreshed next_session_at on {$updated} course(s).");

        return self::SUCCESS;
    }
}
