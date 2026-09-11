<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use Illuminate\Database\Eloquent\Model;

/**
 * Meta tags and robots directives.
 *
 * A package would solve tag rendering, which is the easy part. What this site
 * needs is per-entity overrides, schema gated on verified data, and faceted
 * navigation control — none of which comes out of a box.
 */
class SeoService
{
    /** @return array<string, string|null> */
    public function forModel(Model $model, array $fallbacks = []): array
    {
        $seo = method_exists($model, 'seo') ? $model->seo : null;

        return [
            'title' => $seo?->title ?: ($fallbacks['title'] ?? null),
            'description' => $seo?->description ?: ($fallbacks['description'] ?? null),
            'canonical' => $seo?->canonical_url ?: ($fallbacks['canonical'] ?? null),
            'robots' => $seo?->robots ?: 'index,follow',
            'og_title' => $seo?->og_title ?: $seo?->title ?: ($fallbacks['title'] ?? null),
            'og_image' => $seo?->og_image_path ?: ($fallbacks['og_image'] ?? null),
        ];
    }

    /**
     * Robots directive for a faceted catalogue URL.
     *
     * 524 courses x 12 categories x 26 cities x 3 modes x 3 levels is a
     * combinatorial explosion that Google will happily crawl and index as
     * near-duplicates. One facet is a real, useful landing page; two or more
     * is a filter state that should be followed but not indexed.
     */
    public function facetedRobots(int $activeFacetCount, int $page = 1): string
    {
        $threshold = (int) config('academia.catalogue.indexable_facets', 1);

        if ($page > 1 || $activeFacetCount > $threshold) {
            return 'noindex,follow';
        }

        return 'index,follow';
    }
}
