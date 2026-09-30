<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\LegacyRedirector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 301s for the WordPress URL map, including trailing-slash variants.
 *
 * A trailing slash on any other GET path is also folded away, so a live
 * URL such as /courses/{slug}/ lands on the Laravel route in one hop.
 */
class RedirectLegacyUrls
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->getMethod(), ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        $path = $request->getPathInfo();
        $trimmed = rtrim($path, '/') ?: '/';
        $target = app(LegacyRedirector::class)->target($trimmed, $request->query());

        if (is_string($target)) {
            return redirect()->to($target, 301);
        }

        if ($trimmed !== '/' && $path !== $trimmed) {
            $query = $request->getQueryString();

            return redirect()->to($trimmed.($query ? '?'.$query : ''), 301);
        }

        return $next($request);
    }
}
