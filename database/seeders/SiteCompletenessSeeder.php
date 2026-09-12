<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\Trainer;
use Illuminate\Database\Seeder;

/**
 * Finishing touches that need the CSV catalogue already imported.
 *
 * Marks a balanced featured set for the homepage, and (in local/testing only)
 * publishes a small faculty sample so /trainers is not a deliberate 404 while
 * demos run. Production keeps trainers private until an admin flips them.
 */
class SiteCompletenessSeeder extends Seeder
{
    public function run(): void
    {
        $this->featureCourses();

        if (app()->environment('local', 'testing')) {
            $this->publishDemoTrainers();
        }
    }

    private function featureCourses(): void
    {
        if (Course::query()->published()->featured()->exists()) {
            return;
        }

        $ids = [];

        CourseCategory::query()->active()->ordered()->each(function (CourseCategory $category) use (&$ids): void {
            if (count($ids) >= 8) {
                return;
            }

            $courseId = Course::query()
                ->published()
                ->whereHas('subcategory', fn ($q) => $q->where('course_category_id', $category->id))
                ->orderByDesc('booking_count')
                ->orderBy('title')
                ->value('id');

            if ($courseId) {
                $ids[] = $courseId;
            }
        });

        if ($ids === []) {
            $ids = Course::query()->published()->orderBy('title')->limit(8)->pluck('id')->all();
        }

        if ($ids !== []) {
            Course::query()->whereIn('id', $ids)->update(['is_featured' => true]);
        }
    }

    private function publishDemoTrainers(): void
    {
        Trainer::query()
            ->where('is_public', false)
            ->orderBy('name')
            ->limit(4)
            ->get()
            ->each(function (Trainer $trainer): void {
                $trainer->update([
                    'is_public' => true,
                    'published_at' => $trainer->published_at ?? now(),
                    'status' => 'active',
                ]);
            });
    }
}
