<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\CourseSubcategory;
use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Content\Models\BlogPost;
use App\Domain\Content\Models\CaseStudy;
use App\Domain\Content\Models\GlossaryTerm;
use App\Domain\Content\Services\IndexingPolicy;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(Request $request, IndexingPolicy $indexing): Response
    {
        $lines = ['User-agent: *'];

        if ($indexing->allowsIndexing($request->getHost())) {
            $lines[] = 'Disallow: /thank-you/';
            $lines[] = 'Disallow: /admin/';
            $lines[] = 'Allow: /';
        } else {
            $lines[] = 'Disallow: /';
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.$this->absolute('/sitemap.xml', $request);

        return $this->plain(implode("\n", $lines)."\n", 'text/plain; charset=UTF-8');
    }

    public function sitemap(Request $request): Response
    {
        $xml = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($this->paths() as $path) {
            $loc = htmlspecialchars($this->absolute($path, $request), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $xml[] = '  <url><loc>'.$loc.'</loc></url>';
        }

        $xml[] = '</urlset>';

        return $this->plain(implode("\n", $xml)."\n", 'application/xml; charset=UTF-8');
    }

    /** @return list<string> */
    private function paths(): array
    {
        $paths = [
            route('home', absolute: false),
            route('about', absolute: false),
            route('courses.index', absolute: false),
            route('schedule', absolute: false),
            route('locations', absolute: false),
            route('corporate', absolute: false),
            route('online', absolute: false),
            route('offers', absolute: false),
            route('contact', absolute: false),
            route('skills-credits', absolute: false),
            route('why-our-price', absolute: false),
            route('insights.index', absolute: false),
            route('success-stories.index', absolute: false),
            route('faq', absolute: false),
            route('glossary', absolute: false),
            route('legal', 'privacy', false),
            route('legal', 'terms', false),
            route('legal', 'cancellation-policy', false),
            route('legal', 'cookie-settings', false),
        ];

        foreach (CourseCategory::query()->active()->orderBy('slug')->pluck('slug') as $slug) {
            $paths[] = route('courses.category', $slug, false);
        }

        CourseSubcategory::query()
            ->active()
            ->whereHas('category', fn ($query) => $query->active())
            ->with('category:id,slug')
            ->orderBy('slug')
            ->get(['id', 'course_category_id', 'slug'])
            ->each(function (CourseSubcategory $subcategory) use (&$paths): void {
                $paths[] = route('courses.subcategory', [$subcategory->category->slug, $subcategory->slug], false);
            });

        foreach (Course::query()->published()->orderBy('slug')->pluck('slug') as $slug) {
            $paths[] = route('courses.show', $slug, false);
        }

        foreach (Country::query()->active()->withUpcomingTraining()->orderBy('slug')->pluck('slug') as $slug) {
            $paths[] = route('locations.country', $slug, false);
        }

        City::query()
            ->active()
            ->hasUpcomingSessions()
            ->with('country:id,slug')
            ->orderBy('slug')
            ->get(['id', 'country_id', 'slug'])
            ->each(function (City $city) use (&$paths): void {
                if ($city->country === null) {
                    return;
                }

                $paths[] = route('locations.city', [$city->country->slug, $city->slug], false);
            });

        foreach (BlogPost::query()->published()->orderBy('slug')->pluck('slug') as $slug) {
            $paths[] = route('insights.show', $slug, false);
        }

        foreach (CaseStudy::query()->published()->orderBy('slug')->pluck('slug') as $slug) {
            $paths[] = route('success-stories.show', $slug, false);
        }

        foreach (GlossaryTerm::query()->active()->orderBy('slug')->pluck('slug') as $slug) {
            $paths[] = route('glossary.term', $slug, false);
        }

        $trainers = Trainer::query()->public()->orderBy('slug')->pluck('slug');

        if ($trainers->isNotEmpty()) {
            $paths[] = route('trainers', absolute: false);

            foreach ($trainers as $slug) {
                $paths[] = route('trainers.show', $slug, false);
            }
        }

        return $paths;
    }

    private function absolute(string $path, Request $request): string
    {
        $base = rtrim($request->getSchemeAndHttpHost(), '/');

        if ($path === '' || $path === '/') {
            return $base.'/';
        }

        return $base.'/'.ltrim($path, '/');
    }

    private function plain(string $body, string $contentType): Response
    {
        return response($body, 200, ['Content-Type' => $contentType]);
    }
}
