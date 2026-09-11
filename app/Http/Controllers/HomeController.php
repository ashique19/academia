<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Services\PromotionService;
use App\Domain\Content\Models\BlogPost;
use App\Domain\Content\Models\Testimonial;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * The homepage.
 *
 * Only five sections on this page are Livewire; everything else is static
 * Blade rendered from these cached queries. Each Livewire component adds an
 * initial payload and a hydration snapshot to the DOM, so a homepage built
 * from 18 of them would be slower than the WordPress site it replaces.
 *
 * Cache keys are invalidated by model observers on save, not by TTL alone —
 * a course published at 09:00 must not be invisible until 10:00.
 */
class HomeController extends Controller
{
    public function __invoke(PromotionService $promotions): View
    {
        return view('pages.home', [
            'promotion' => $promotions->active(),

            'categories' => Cache::remember('home.categories', now()->addHour(), fn () =>
                CourseCategory::active()->ordered()->withPublishedCourseCount()->get()),

            'featured' => Cache::remember('home.featured', now()->addHour(), fn () =>
                Course::published()->featured()->withCardRelations()
                    ->orderByDesc('booking_count')->take(8)->get()),

            'popular' => Cache::remember('home.popular', now()->addHour(), fn () =>
                Course::published()->withCardRelations()
                    ->orderByDesc('booking_count')->take(8)->get()),

            'upcoming' => Cache::remember('home.upcoming', now()->addMinutes(30), fn () =>
                CourseSchedule::query()->upcoming()->bookable()->withListRelations()
                    ->orderBy('starts_at')->take(8)->get()),

            'cities' => Cache::remember('home.cities', now()->addHours(6), fn () =>
                City::active()->hasUpcomingSessions()->withUpcomingSessionCount()
                    ->with('country')->orderByDesc('upcoming_sessions_count')->take(12)->get()),

            'testimonials' => Cache::remember('home.testimonials', now()->addHours(6), fn () =>
                Testimonial::published()->orderBy('sort_order')->take(3)->get()),

            'posts' => Cache::remember('home.posts', now()->addHour(), fn () =>
                BlogPost::published()->with('category')->latest('published_at')->take(3)->get()),

            'stats' => Cache::remember('home.stats', now()->addHours(6), fn () => [
                'courses' => Course::published()->count(),
                'cities'  => City::active()->hasUpcomingSessions()->count(),
            ]),
        ]);
    }
}
