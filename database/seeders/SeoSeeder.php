<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Content\Models\BlogPost;
use App\Domain\Shared\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Per-entity SEO rows for catalogue and content surfaces.
 *
 * Idempotent: only fills missing title/description so hand-edited admin SEO
 * is preserved on re-seed. Run after `academia:import` (see composer setup /
 * PostImportSeeder) so courses, categories and cities exist.
 */
class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCourses();
        $this->seedCategories();
        $this->seedCities();
        $this->seedBlogPosts();
    }

    private function seedCourses(): void
    {
        Course::query()->published()->with('seo')->orderBy('id')->chunkById(100, function ($courses): void {
            foreach ($courses as $course) {
                $title = Str::limit($course->title.' | Academia Training Solutions', 60, '');
                $description = $this->metaDescription(
                    $course->summary
                        ?: "Book {$course->title} with Academia Training Solutions — expert-led professional training across Europe."
                );

                $this->upsertSeo($course, [
                    'title' => $title,
                    'description' => $description,
                    'keywords' => implode(', ', array_filter([
                        $course->title,
                        $course->code,
                        'professional training',
                        'Europe',
                    ])),
                    'og_title' => $title,
                    'og_description' => $description,
                    'canonical_url' => url('/courses/'.$course->slug),
                    'robots' => 'index,follow',
                ]);
            }
        });
    }

    private function seedCategories(): void
    {
        CourseCategory::query()->active()->with('seo')->each(function (CourseCategory $category): void {
            $title = Str::limit($category->name.' courses | Academia Training Solutions', 60, '');
            $description = $this->metaDescription(
                $category->summary
                    ?: "Browse {$category->name} courses from Academia Training Solutions — classroom, online and in-company delivery across Europe."
            );

            $this->upsertSeo($category, [
                'title' => $title,
                'description' => $description,
                'keywords' => "{$category->name}, professional training, Academia",
                'og_title' => $title,
                'og_description' => $description,
                'canonical_url' => url('/categories/'.$category->slug),
                'robots' => 'index,follow',
            ]);
        });
    }

    private function seedCities(): void
    {
        City::query()->active()->with(['seo', 'country'])->each(function (City $city): void {
            $place = $city->country?->name
                ? "{$city->name}, {$city->country->name}"
                : $city->name;
            $title = Str::limit("Training in {$place} | Academia Training Solutions", 60, '');
            $description = $this->metaDescription(
                $city->intro
                    ?: "Upcoming classroom courses in {$place}. Book public seats or arrange in-company delivery with Academia Training Solutions."
            );

            $this->upsertSeo($city, [
                'title' => $title,
                'description' => $description,
                'keywords' => "{$city->name}, classroom training, professional courses",
                'og_title' => $title,
                'og_description' => $description,
                'canonical_url' => url('/cities/'.$city->slug),
                'robots' => 'index,follow',
            ]);
        });
    }

    private function seedBlogPosts(): void
    {
        BlogPost::query()->published()->with('seo')->each(function (BlogPost $post): void {
            $title = Str::limit($post->title.' | Academia Training Solutions', 60, '');
            $description = $this->metaDescription(
                $post->excerpt
                    ?: Str::limit(strip_tags((string) $post->body), 155)
            );

            $this->upsertSeo($post, [
                'title' => $title,
                'description' => $description,
                'keywords' => $post->title.', training insights, Academia',
                'og_title' => $title,
                'og_description' => $description,
                'canonical_url' => url('/blog/'.$post->slug),
                'robots' => 'index,follow',
            ]);
        });
    }

    /** @param  array<string, string|null>  $attributes */
    private function upsertSeo(Course|CourseCategory|City|BlogPost $model, array $attributes): void
    {
        $existing = $model->seo;

        if ($existing === null) {
            $model->seo()->create($attributes);

            return;
        }

        // Fill blanks only — never overwrite curated admin SEO.
        $patch = [];
        foreach ($attributes as $key => $value) {
            if (blank($existing->{$key}) && filled($value)) {
                $patch[$key] = $value;
            }
        }

        if ($patch !== []) {
            $existing->update($patch);
        }
    }

    private function metaDescription(string $text): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', strip_tags($text)) ?? '');

        return Str::limit($clean, 155, '…');
    }
}
