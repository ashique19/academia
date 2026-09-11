<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Search is cheap but scriptable; forms are expensive and abusable.
        RateLimiter::for('search', fn (Request $request) =>
            Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('forms', fn (Request $request) =>
            Limit::perHour(10)->by($request->ip()));

        RateLimiter::for('login', fn (Request $request) =>
            Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email')));
    }
}
