<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

/**
 * Whether the host serving this request may be indexed.
 *
 * Production hosts stay indexable even when APP_ENV is not production, so a
 * staging flag cannot leak onto academiatraining.eu. The x. host — and any
 * host starting with "x." — stays non-indexable even when APP_ENV is
 * production, which is how the staging box is often deployed.
 *
 * Every other host is indexable only in the production environment.
 */
final class IndexingPolicy
{
    public const NOINDEX = 'noindex,nofollow';

    /** @var list<string> */
    private const PRODUCTION_HOSTS = [
        'academiatraining.eu',
        'www.academiatraining.eu',
    ];

    public function allowsIndexing(?string $host = null): bool
    {
        $host = $this->normalizeHost($host ?? request()->getHost());

        if (in_array($host, $this->productionHosts(), true)) {
            return true;
        }

        if ($this->isStagingHost($host)) {
            return false;
        }

        return app()->environment('production');
    }

    /**
     * Robots directive for a page. Non-indexable hosts always get
     * noindex,nofollow. Indexable hosts keep the page's own directive
     * (thank-you pages stay noindex,follow; faceted catalogue URLs stay
     * noindex,follow).
     */
    public function directive(?string $pageDirective = null): string
    {
        if (! $this->allowsIndexing()) {
            return self::NOINDEX;
        }

        $pageDirective = trim((string) $pageDirective);

        return $pageDirective !== '' ? $pageDirective : 'index,follow';
    }

    /**
     * Absolute canonical on an indexable host. Staging hosts get none, so a
     * noindex page is not also advertised as the production URL.
     */
    public function canonicalUrl(?string $override = null): ?string
    {
        if (! $this->allowsIndexing()) {
            return null;
        }

        $override = trim((string) $override);

        if ($override !== '') {
            return $override;
        }

        $path = '/'.ltrim(request()->getPathInfo(), '/');
        $path = rtrim($path, '/') ?: '/';

        return 'https://'.self::PRODUCTION_HOSTS[0].$path;
    }

    /** @return list<string> */
    private function productionHosts(): array
    {
        $extra = config('academia.seo.production_hosts', []);

        return array_values(array_unique(array_merge(
            self::PRODUCTION_HOSTS,
            $this->hostList(is_array($extra) ? $extra : []),
        )));
    }

    private function isStagingHost(string $host): bool
    {
        if (str_starts_with($host, 'x.')) {
            return true;
        }

        $configured = config('academia.seo.staging_hosts', ['x.academiatraining.eu']);

        return in_array($host, $this->hostList(is_array($configured) ? $configured : []), true);
    }

    /**
     * @param  array<int, mixed>  $hosts
     * @return list<string>
     */
    private function hostList(array $hosts): array
    {
        $normalized = [];

        foreach ($hosts as $host) {
            if (! is_string($host) || trim($host) === '') {
                continue;
            }

            $normalized[] = $this->normalizeHost($host);
        }

        return $normalized;
    }

    private function normalizeHost(string $host): string
    {
        $host = strtolower(trim($host));
        $host = rtrim($host, '.');

        return preg_replace('/:\d+$/', '', $host) ?? $host;
    }
}
