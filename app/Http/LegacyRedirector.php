<?php

declare(strict_types=1);

namespace App\Http;

use App\Domain\Content\Models\GlossaryTerm;
use Illuminate\Support\Str;

/**
 * Resolves a legacy WordPress path to a relative Laravel URL.
 *
 * Returns null when the path is not a legacy URL. City and country slugs
 * come from database/data/locations.csv (the 26 classroom cities). Expertise
 * tags go to a glossary term when that slug exists, otherwise to catalogue
 * search. Nothing here invents a page.
 */
final class LegacyRedirector
{
    /** @var array<string, array{0: string, 1: string}>|null */
    private ?array $cities = null;

    /** @var array<string, array{0: string, 1: string}>|null */
    private ?array $subcategories = null;

    /**
     * @param  array<string, mixed>  $query
     */
    public function target(string $path, array $query = []): ?string
    {
        $path = '/'.trim($path, '/');

        if ($path === '/training-catalogue') {
            return $this->catalogue($query);
        }

        if ($path === '/training-schedule') {
            return '/schedule';
        }

        if ($path === '/classroom-training-europe') {
            return '/classroom-training';
        }

        if (preg_match('#^/locations/([^/]+)$#', $path, $match) === 1) {
            return $this->city($match[1]);
        }

        if (preg_match('#^/training-in/([^/]+)$#', $path, $match) === 1) {
            return $this->country($match[1]);
        }

        if (preg_match('#^/delivery/([^/]+)$#', $path, $match) === 1) {
            return $this->delivery($match[1]);
        }

        if (preg_match('#^/expertise/([^/]+)$#', $path, $match) === 1) {
            return $this->expertise($match[1]);
        }

        if (preg_match('#^/training/([^/]+)(?:/([^/]+))?$#', $path, $match) === 1) {
            return $this->training($match[1], $match[2] ?? null);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function catalogue(array $query): string
    {
        $sub = $this->stringQuery($query, 'sub') ?? $this->stringQuery($query, 'subcategory');
        $cat = $this->stringQuery($query, 'cat') ?? $this->stringQuery($query, 'category');
        $search = $this->stringQuery($query, 'q');

        if ($sub !== null) {
            return $this->training($cat ?? '', $sub);
        }

        if ($cat !== null) {
            $target = $this->training($cat, null);

            if ($target !== '/courses' || $search === null) {
                return $target;
            }
        }

        if ($search !== null) {
            return '/courses?'.http_build_query(['q' => $search]);
        }

        return '/courses';
    }

    private function training(string $parent, ?string $child): string
    {
        $child = $child !== null ? Str::slug($child) : null;

        if ($child !== null && $child !== '') {
            $mapped = $this->subcategories()[$child] ?? null;

            if ($mapped !== null) {
                return '/courses/category/'.$mapped[0].'/'.$mapped[1];
            }

            return '/courses?'.http_build_query(['q' => str_replace('-', ' ', $child)]);
        }

        $parent = Str::slug($parent);
        $parents = config('legacy_redirects.parents', []);
        $category = is_array($parents) && array_key_exists($parent, $parents)
            ? $parents[$parent]
            : null;

        if (is_string($category) && $category !== '') {
            return '/courses/category/'.$category;
        }

        return '/courses';
    }

    private function city(string $slug): string
    {
        $slug = Str::slug($slug);
        $mapped = $this->cities()[$slug] ?? null;

        if ($mapped === null) {
            return '/classroom-training';
        }

        return '/classroom-training/'.$mapped[0].'/'.$mapped[1];
    }

    private function country(string $slug): string
    {
        $slug = Str::slug($slug);

        foreach ($this->cities() as [$country]) {
            if ($country === $slug) {
                return '/classroom-training/'.$country;
            }
        }

        return '/classroom-training';
    }

    private function delivery(string $mode): string
    {
        $mode = Str::slug($mode);
        $targets = config('legacy_redirects.delivery', []);

        if (is_array($targets) && isset($targets[$mode]) && is_string($targets[$mode])) {
            return $targets[$mode];
        }

        return '/courses';
    }

    private function expertise(string $tag): string
    {
        $tag = Str::slug($tag);

        if ($tag === '') {
            return '/courses';
        }

        $known = GlossaryTerm::query()->active()->where('slug', $tag)->exists();

        if ($known) {
            return '/glossary/'.$tag;
        }

        return '/courses?'.http_build_query(['q' => str_replace('-', ' ', $tag)]);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function stringQuery(array $query, string $key): ?string
    {
        $value = $query[$key] ?? null;

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private function subcategories(): array
    {
        if ($this->subcategories !== null) {
            return $this->subcategories;
        }

        $map = [];

        foreach (config('academia_taxonomy.map', []) as $categoryName => $definition) {
            if (! is_string($categoryName) || ! is_array($definition)) {
                continue;
            }

            $categorySlug = Str::slug($categoryName);

            foreach ($definition['subcategories'] ?? [] as $subcategoryName) {
                if (! is_string($subcategoryName)) {
                    continue;
                }

                $slug = Str::slug($subcategoryName);
                $map[$slug] = [$categorySlug, $slug];
            }
        }

        return $this->subcategories = $map;
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    private function cities(): array
    {
        if ($this->cities !== null) {
            return $this->cities;
        }

        $path = database_path('data/locations.csv');
        $cities = [];

        if (! is_readable($path)) {
            return $this->cities = $cities;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            return $this->cities = $cities;
        }

        $header = fgetcsv($handle);

        if (! is_array($header)) {
            fclose($handle);

            return $this->cities = $cities;
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                continue;
            }

            $data = array_combine($header, $row);

            if (! is_array($data)) {
                continue;
            }

            $city = Str::slug((string) ($data['city'] ?? ''));
            $country = Str::slug((string) ($data['country'] ?? ''));

            if ($city === '' || $country === '') {
                continue;
            }

            $cities[$city] = [$country, $city];
        }

        fclose($handle);

        return $this->cities = $cities;
    }
}
