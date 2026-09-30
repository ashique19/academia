<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Content\Services\IndexingPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Staging and other non-production hosts must not be indexed.
 *
 * The header covers responses that do not render the public layout (admin,
 * errors, Livewire). Production hosts are left alone so a page can still
 * emit its own robots directive, such as noindex,follow on thank-you URLs.
 */
class ApplyIndexingPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! app(IndexingPolicy::class)->allowsIndexing($request->getHost())) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
