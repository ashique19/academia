<?php

declare(strict_types=1);

use App\Domain\Catalogue\Models\Course;
use App\Domain\Content\Models\BlogPost;
use App\Domain\Content\Models\CaseStudy;
use App\Domain\Content\Models\SeoMetadata;
use App\Domain\Shared\Models\Setting;
use Database\Seeders\BlogCategorySeeder;
use Database\Seeders\BlogPostSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\SeoSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\SiteCompletenessSeeder;

it('seeds site settings for the admin panel', function () {
    $this->seed(SettingSeeder::class);

    expect(Setting::query()->count())->toBeGreaterThan(0)
        ->and(Setting::get('seo.default_title'))->toMatchArray([
            'value' => 'Academia Training Solutions — Professional Training Across Europe',
        ]);
});

it('seeds published blog posts and case studies', function () {
    $this->seed(DatabaseSeeder::class);

    expect(BlogPost::query()->published()->count())->toBeGreaterThanOrEqual(4)
        ->and(CaseStudy::query()->published()->count())->toBe(3)
        ->and(SeoMetadata::query()->where('seoable_type', BlogPost::class)->count())
        ->toBeGreaterThanOrEqual(4);
});

it('creates SEO rows for published courses and marks featured courses', function () {
    $this->seed(DatabaseSeeder::class);

    $course = Course::factory()->create([
        'status' => 'published',
        'published_at' => now(),
        'title' => 'SEO Seed Course',
        'summary' => 'A concise summary used for the meta description on the course page.',
        'is_featured' => false,
    ]);

    $this->seed(SiteCompletenessSeeder::class);
    $this->seed(SeoSeeder::class);

    $course->refresh();

    expect($course->seo)->not->toBeNull()
        ->and($course->seo->description)->not->toBeEmpty()
        ->and($course->seo->title)->toContain('SEO Seed Course')
        ->and(Course::query()->featured()->count())->toBeGreaterThan(0);
});

it('does not overwrite curated SEO on re-seed', function () {
    $this->seed([BlogCategorySeeder::class, BlogPostSeeder::class]);

    $post = BlogPost::query()->firstOrFail();
    $post->seo()->updateOrCreate([], [
        'title' => 'Curated title from admin',
        'description' => 'Curated description from admin that must survive re-seed.',
        'robots' => 'index,follow',
    ]);

    $this->seed(SeoSeeder::class);

    $post->refresh();

    expect($post->seo->title)->toBe('Curated title from admin')
        ->and($post->seo->description)->toBe('Curated description from admin that must survive re-seed.');
});
