<?php

declare(strict_types=1);

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\CourseSubcategory;
use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Content\Models\GlossaryTerm;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PageController;
use App\Livewire\Public\CourseCatalogue;
use App\Livewire\Public\ScheduleBrowser;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
|
| Note what is NOT here: there is no route for a CourseSchedule. Sessions
| are deliberately not addressable — 1,561 dated pages that die on their end
| date would leak authority away from the course pages that accumulate it.
| Sessions surface inside course, schedule and city pages instead.
|
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');

/* ------------------------------------------------------------- catalogue */

Route::get('/courses', CourseCatalogue::class)->name('courses.index');

Route::get('/courses/category/{category}', [CourseController::class, 'category'])
    ->name('courses.category');

Route::get('/courses/category/{category}/{subcategory}', [CourseController::class, 'subcategory'])
    ->name('courses.subcategory');

Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

// Course x city. 404s unless a real session exists — the doorway-page gate.
Route::get('/courses/{course}/{city}', [CourseController::class, 'showInCity'])
    ->name('courses.city');

/* -------------------------------------------------------------- schedule */

Route::get('/schedule', ScheduleBrowser::class)->name('schedule');

/* ------------------------------------------------------------- locations */

Route::get('/classroom-training', [LocationController::class, 'index'])->name('locations');
Route::get('/classroom-training/{country}', [LocationController::class, 'country'])->name('locations.country');
Route::get('/classroom-training/{country}/{city}', [LocationController::class, 'city'])->name('locations.city');

/* ------------------------------------------------------- delivery modes */

Route::get('/corporate-training', [PageController::class, 'corporate'])->name('corporate');
Route::get('/online-training', [PageController::class, 'online'])->name('online');

/* --------------------------------------------------------------- offers */

Route::get('/offers', [PageController::class, 'offers'])->name('offers');

/* -------------------------------------------------------------- content */

Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/glossary', [PageController::class, 'glossary'])->name('glossary');
Route::get('/glossary/{term}', [PageController::class, 'glossaryTerm'])->name('glossary.term');

/* ------------------------------------------------------------- trainers */
// These 404 entirely when no trainer is marked public — which is the
// default. See spec §3.1: trainer visibility is an open business decision.

Route::get('/trainers', [PageController::class, 'trainers'])->name('trainers');
Route::get('/trainers/{trainer}', [PageController::class, 'trainer'])->name('trainers.show');

/* --------------------------------------------------------- conversion */

Route::get('/thank-you/{type}', [PageController::class, 'thankYou'])->name('thank-you');

/* ------------------------------------------------------------- legal */

Route::get('/{document}', [PageController::class, 'legal'])
    ->whereIn('document', ['privacy', 'terms', 'cancellation-policy', 'cookie-settings'])
    ->name('legal');

/*
|--------------------------------------------------------------------------
| Route model binding
|--------------------------------------------------------------------------
|
| Bindings are scoped to published state, so an unpublished course is not
| reachable by guessing its slug. Admin preview uses a signed URL rather
| than a status bypass here — a bypass in the binding is a bypass for
| everyone.
|
*/

Route::bind('course', fn (string $slug) => Course::query()
    ->published()
    ->where('slug', $slug)
    ->firstOrFail());

Route::bind('category', fn (string $slug) => CourseCategory::query()
    ->active()
    ->where('slug', $slug)
    ->firstOrFail());

Route::bind('subcategory', fn (string $slug) => CourseSubcategory::query()
    ->active()
    ->where('slug', $slug)
    ->firstOrFail());

Route::bind('country', fn (string $slug) => Country::query()
    ->active()
    ->where('slug', $slug)
    ->firstOrFail());

Route::bind('city', fn (string $slug) => City::query()
    ->active()
    ->where('slug', $slug)
    ->firstOrFail());

Route::bind('trainer', fn (string $slug) => Trainer::query()
    ->public()
    ->where('slug', $slug)
    ->firstOrFail());

Route::bind('term', fn (string $slug) => GlossaryTerm::query()
    ->active()
    ->where('slug', $slug)
    ->firstOrFail());
