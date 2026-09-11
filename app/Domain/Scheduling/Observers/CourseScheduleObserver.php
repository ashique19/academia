<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Observers;

use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Models\CourseSchedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseScheduleObserver
{
    public function saved(CourseSchedule $schedule): void
    {
        $this->refreshNextSession($schedule->course_id);
        $this->flushCaches();
    }

    public function deleted(CourseSchedule $schedule): void
    {
        $this->refreshNextSession($schedule->course_id);
        $this->flushCaches();
    }

    /**
     * Maintain courses.next_session_at.
     *
     * This is the denormalisation that makes the catalogue fast: "soonest
     * upcoming open session" is a per-row aggregate that eager loading cannot
     * solve, so 24 course cards would otherwise be 24 extra queries. It also
     * turns sort=soonest into an indexed ORDER BY.
     *
     * A nightly command recomputes the whole column as a safety net, because
     * an observer misses writes made by a raw query.
     */
    private function refreshNextSession(?int $courseId): void
    {
        if ($courseId === null) {
            return;
        }

        $next = DB::table('course_schedules')
            ->where('course_id', $courseId)
            ->where('status', ScheduleStatus::Open->value)
            ->where('starts_at', '>', now())
            ->whereNull('deleted_at')
            ->min('starts_at');

        DB::table('courses')->where('id', $courseId)->update(['next_session_at' => $next]);
    }

    private function flushCaches(): void
    {
        foreach (['home.upcoming', 'home.cities', 'catalogue.cities'] as $key) {
            Cache::forget($key);
        }
    }
}
