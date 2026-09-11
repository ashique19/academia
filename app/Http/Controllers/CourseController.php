<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\CourseSubcategory;
use App\Domain\Catalogue\Services\PromotionService;
use App\Domain\Shared\Models\City;
use Illuminate\Contracts\View\View;

class CourseController extends Controller
{
    public function show(Course $course, PromotionService $promotions): View
    {
        $course->load([
            'subcategory.category', 'deliveryModes', 'modules', 'scheme', 'faqs',
            'schedules' => fn ($q) => $q->upcoming()->publiclyVisible()
                ->with(['course.scheme', 'city.country', 'venue', 'deliveryMode'])->orderBy('starts_at')->take(12),
        ]);

        // Fire-and-forget popularity counter. No model events, no updated_at
        // churn — a view is not an edit.
        $course->newQuery()->whereKey($course->id)->increment('view_count');

        return view('pages.course', [
            'course' => $course,
            'price' => $promotions->priceFor($course, 1, $course->next_session?->starts_at),
            'related' => Course::published()
                ->where('course_subcategory_id', $course->course_subcategory_id)
                ->whereKeyNot($course->id)
                ->withCardRelations()
                ->take(4)->get(),
        ]);
    }

    /**
     * Course x city landing page.
     *
     * 404s unless a real session exists for this course in this city. That
     * check is the entire difference between a local landing page and a
     * doorway page, and doorway pages are a manual-action category — so it
     * lives in code where growth pressure cannot quietly remove it.
     */
    public function showInCity(Course $course, City $city, PromotionService $promotions): View
    {
        $sessions = $course->schedules()
            ->upcoming()->bookable()
            ->where('city_id', $city->id)
            ->with(['venue', 'deliveryMode'])
            ->orderBy('starts_at')
            ->get();

        abort_if($sessions->isEmpty(), 404);

        return view('pages.course-city', [
            'course' => $course->load(['subcategory.category', 'modules', 'scheme']),
            'city' => $city->load('country', 'venues'),
            'sessions' => $sessions,
            'price' => $promotions->priceFor($course, 1, $sessions->first()?->starts_at),
        ]);
    }

    public function category(CourseCategory $category): View
    {
        abort_unless($category->is_active, 404);

        return view('pages.category', [
            'category' => $category->load('subcategories'),
            'subcategories' => $category->subcategories()
                ->withCount(['courses as published_courses_count' => fn ($q) => $q->published()])
                ->get(),
        ]);
    }

    public function subcategory(CourseCategory $category, CourseSubcategory $subcategory): View
    {
        abort_unless(
            $subcategory->course_category_id === $category->id && $subcategory->is_active,
            404
        );

        return view('pages.subcategory', compact('category', 'subcategory'));
    }
}
