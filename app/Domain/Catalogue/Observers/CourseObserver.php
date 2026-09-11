<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Observers;

use App\Domain\Catalogue\Enums\CourseStatus;
use App\Domain\Catalogue\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourseObserver
{
    /**
     * A published slug is frozen.
     *
     * Changing it silently breaks every inbound link and every ranking that
     * took months to earn. Rather than refuse the edit, we allow it and write
     * a 301 — but never silently.
     */
    public function updating(Course $course): void
    {
        if (! $course->isDirty('slug')) {
            return;
        }

        $original = $course->getOriginal('slug');
        $wasPublished = $course->getOriginal('status') === CourseStatus::Published->value
            || $course->getOriginal('status') === CourseStatus::Published;

        if (! $wasPublished || blank($original)) {
            return;
        }

        DB::table('redirects')->updateOrInsert(
            ['from_path' => '/courses/' . $original],
            [
                'to_path'     => '/courses/' . $course->slug,
                'status_code' => 301,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );
    }

    public function saved(Course $course): void
    {
        $this->flushCaches();
    }

    public function deleted(Course $course): void
    {
        $this->flushCaches();
    }

    /**
     * Invalidate on write, not on TTL alone.
     *
     * A course published at 09:00 must not be invisible until 10:00 — an
     * editor who cannot see their own change assumes the site is broken.
     */
    private function flushCaches(): void
    {
        foreach ([
            'home.categories', 'home.featured', 'home.popular', 'home.stats',
            'catalogue.categories',
        ] as $key) {
            Cache::forget($key);
        }
    }
}
