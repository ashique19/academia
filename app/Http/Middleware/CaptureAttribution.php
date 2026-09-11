<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * First-touch attribution.
 *
 * Twenty lines that make "leads by source" a fact rather than a guess, which
 * is the highest-ROI analytics decision in the project. FIRST touch, not last:
 * once a source is recorded for this session it is never overwritten, so a
 * visitor who arrives from a search ad and returns directly a week later is
 * still credited to the ad.
 */
class CaptureAttribution
{
    private const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->hasSession() || $request->session()->has('attribution.captured_at')) {
            return $next($request);
        }

        $attribution = [];

        foreach (self::KEYS as $key) {
            if ($request->filled($key)) {
                $attribution[$key] = mb_substr((string) $request->query($key), 0, 120);
            }
        }

        $attribution['source'] = $attribution['utm_source'] ?? $this->classifyReferrer($request);
        $attribution['landing'] = mb_substr($request->path(), 0, 200);
        $attribution['captured_at'] = now()->toIso8601String();

        $request->session()->put('attribution', $attribution);

        return $next($request);
    }

    private function classifyReferrer(Request $request): string
    {
        $referrer = $request->headers->get('referer');

        if (blank($referrer)) {
            return 'direct';
        }

        $host = parse_url($referrer, PHP_URL_HOST) ?: '';

        if ($host === '' || str_contains($host, $request->getHost())) {
            return 'direct';
        }

        return match (true) {
            str_contains($host, 'google.') => 'google',
            str_contains($host, 'bing.') => 'bing',
            str_contains($host, 'duckduckgo') => 'duckduckgo',
            str_contains($host, 'linkedin.') => 'linkedin',
            default => mb_substr($host, 0, 80),
        };
    }
}
