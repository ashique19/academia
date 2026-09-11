<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Observers\CourseObserver;
use App\Domain\Catalogue\Observers\TestimonialObserver;
use App\Domain\Content\Models\Testimonial;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Scheduling\Observers\CourseScheduleObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Course::observe(CourseObserver::class);
        CourseSchedule::observe(CourseScheduleObserver::class);
        Testimonial::observe(TestimonialObserver::class);

        /*
         * Strict mode outside production.
         *
         * preventLazyLoading is the important one: an N+1 then THROWS during
         * development instead of quietly shipping. The catalogue renders 24
         * cards, each touching four relations — without this the regression
         * is invisible until the page is slow in production.
         */
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
        Model::shouldBeStrict(! app()->isProduction());

        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        /*
         * Super admin bypasses every gate. Deliberately the ONLY blanket
         * grant in the system — see RoleSeeder for why admins cannot assign
         * roles to themselves.
         */
        Gate::before(fn ($user) => $user->hasRole('super-admin') ? true : null);
    }
}
