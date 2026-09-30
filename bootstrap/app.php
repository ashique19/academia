<?php

use App\Http\Middleware\ApplyIndexingPolicy;
use App\Http\Middleware\CaptureAttribution;
use App\Http\Middleware\RedirectLegacyUrls;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Before routing, so legacy paths and trailing slashes 301 instead of 404.
        $middleware->prepend(RedirectLegacyUrls::class);

        // First-touch UTM capture. Without this, "leads by source" reporting
        // is a guess — see spec §17.5.
        $middleware->web(append: [
            CaptureAttribution::class,
            ApplyIndexingPolicy::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
