<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Course search, behind an interface-shaped seam.
 *
 * ---------------------------------------------------------------------------
 * WHY THERE IS NO MEILISEARCH HERE
 *
 * 524 courses is a small table. A Postgres tsvector GIN index answers in
 * single-digit milliseconds with proper ranking and stemming. Meilisearch
 * would add a service to run, monitor, back up, keep in sync and re-index
 * after every catalogue import — for a corpus that fits in the database's
 * page cache.
 *
 * Install Scout + Meilisearch when ONE of these becomes true:
 *   - the catalogue passes ~5,000 courses
 *   - typo tolerance ("prince2 fondation") is wanted as a conversion feature
 *   - search must span courses + blog + glossary + trainers in one ranked set
 *   - p95 search latency exceeds 150ms
 *
 * Because every caller goes through this class, that swap is a container
 * binding rather than a refactor. One hour of foresight buys the option.
 * ---------------------------------------------------------------------------
 */
class CourseSearchService
{
    public function apply(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return match (DB::getDriverName()) {
            'pgsql' => $this->postgresFullText($query, $term),
            'mysql' => $this->mysqlFullText($query, $term),
            default => $this->likeFallback($query, $term),
        };
    }

    private function postgresFullText(Builder $query, string $term): Builder
    {
        return $query
            ->whereRaw("search_vector @@ plainto_tsquery('english', ?)", [$term])
            ->orderByRaw("ts_rank(search_vector, plainto_tsquery('english', ?)) DESC", [$term]);
    }

    private function mysqlFullText(Builder $query, string $term): Builder
    {
        return $query
            ->whereRaw('MATCH(title, summary) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
            ->orderByRaw('MATCH(title, summary) AGAINST (? IN NATURAL LANGUAGE MODE) DESC', [$term]);
    }

    /**
     * SQLite and anything else. Deliberately ranks a title hit above a
     * summary hit rather than treating all matches as equal, because a
     * course whose *title* matches is almost always what was meant.
     */
    private function likeFallback(Builder $query, string $term): Builder
    {
        $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';

        return $query
            ->where(fn (Builder $q) => $q
                ->where('title', 'like', $like)
                ->orWhere('summary', 'like', $like))
            ->orderByRaw('CASE WHEN title LIKE ? THEN 0 ELSE 1 END', [$like]);
    }
}
