<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Catalogue\Services\CourseSearchService;
use App\Domain\Content\Services\SeoService;
use App\Domain\Shared\Models\City;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The faceted course catalogue.
 *
 * ---------------------------------------------------------------------------
 * EVERY FACET IS URL-ADDRESSABLE. This is the single most important decision
 * in the component and the one most often missed.
 *
 * Filter state lives in the query string via #[Url], which makes a filtered
 * view shareable, bookmarkable, correct under the back button, and visible to
 * analytics. State held only in the component is none of those things, and
 * "our catalogue gets no organic traffic" usually traces back to exactly this.
 * ---------------------------------------------------------------------------
 */
class CourseCatalogue extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'subcategory', except: '')]
    public string $subcategory = '';

    /** @var array<int, string> */
    #[Url(as: 'mode', except: [])]
    public array $modes = [];

    /** @var array<int, string> */
    #[Url(as: 'level', except: [])]
    public array $levels = [];

    /** @var array<int, string> */
    #[Url(as: 'city', except: [])]
    public array $cities = [];

    #[Url(as: 'sort', except: 'popular')]
    public string $sort = 'popular';

    public function mount(?string $category = null, ?string $subcategory = null): void
    {
        $this->category    = $category ?? $this->category;
        $this->subcategory = $subcategory ?? $this->subcategory;
    }

    /**
     * Reset pagination whenever a filter changes.
     *
     * Without this, filtering from page 7 down to 12 results shows an empty
     * page 7 and looks broken. It is the most common Livewire catalogue bug.
     */
    public function updated(string $property): void
    {
        if ($property !== 'page') {
            $this->resetPage();
        }
    }

    public function toggleFacet(string $facet, string $value): void
    {
        if (! in_array($facet, ['modes', 'levels', 'cities'], true)) {
            return;
        }

        $current = $this->{$facet};

        $this->{$facet} = in_array($value, $current, true)
            ? array_values(array_diff($current, [$value]))
            : [...$current, $value];

        $this->resetPage();
    }

    public function clearFacet(string $facet, ?string $value = null): void
    {
        match ($facet) {
            'search'      => $this->search = '',
            'category'    => $this->category = '',
            'subcategory' => $this->subcategory = '',
            'all'         => $this->resetFilters(),
            default       => $this->{$facet} = $value === null
                ? []
                : array_values(array_diff($this->{$facet}, [$value])),
        };

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'subcategory', 'modes', 'levels', 'cities']);
        $this->sort = 'popular';
        $this->resetPage();
    }

    /* ------------------------------------------------------------- queries */

    /** The filtered query, without pagination — reused by the facet counter. */
    private function baseQuery(): Builder
    {
        return Course::query()
            ->published()
            ->when($this->search, fn (Builder $q) => app(CourseSearchService::class)->apply($q, $this->search))
            ->when($this->category, fn (Builder $q, $slug) => $q->inCategory($slug))
            ->when($this->subcategory, fn (Builder $q, $slug) => $q->whereHas(
                'subcategory', fn (Builder $s) => $s->where('slug', $slug)
            ))
            ->when($this->modes, fn (Builder $q, $modes) => $q->withDeliveryMode($modes))
            ->when($this->levels, fn (Builder $q, $levels) => $q->whereIn('level', $levels))
            ->when($this->cities, fn (Builder $q, $cities) => $q->whereHas(
                'schedules',
                fn (Builder $s) => $s->upcoming()->bookable()
                    ->whereHas('city', fn (Builder $c) => $c->whereIn('slug', $cities))
            ));
    }

    #[Computed]
    public function courses(): LengthAwarePaginator
    {
        return $this->applySort($this->baseQuery()->withCardRelations())
            ->paginate(config('academia.catalogue.per_page', 24))
            ->withQueryString();
    }

    private function applySort(Builder $query): Builder
    {
        return match ($this->sort) {
            // next_session_at is denormalised precisely so this is an indexed
            // ORDER BY rather than a correlated subquery per row.
            'soonest'    => $query->whereNotNull('next_session_at')->orderBy('next_session_at'),
            'price-asc'  => $query->orderByRaw('price_cents IS NULL, price_cents ASC'),
            'price-desc' => $query->orderByRaw('price_cents IS NULL, price_cents DESC'),
            'az'         => $query->orderBy('title'),
            'newest'     => $query->orderByDesc('published_at'),
            default      => $query->orderByDesc('booking_count')->orderByDesc('view_count'),
        };
    }

    /**
     * Facet counts, computed in as few queries as possible and cached.
     *
     * Counting per option naively is a dozen extra aggregates on every page
     * load. Options with zero results are shown greyed out rather than hidden:
     * a facet that vanishes when you pick another one reads as a broken site.
     */
    #[Computed]
    public function facetCounts(): array
    {
        $key = 'facets:' . md5(serialize($this->activeFilters()));

        return Cache::remember(
            $key,
            now()->addHours((int) config('academia.catalogue.facet_cache_hours', 1)),
            fn () => [
                'levels' => $this->baseQuery()
                    ->reorder()
                    ->groupBy('level')
                    ->selectRaw('level, COUNT(*) as total')
                    ->pluck('total', 'level')
                    ->all(),

                'modes' => DeliveryMode::query()
                    ->withCount(['courses as total' => fn (Builder $q) => $q->published()])
                    ->pluck('total', 'slug')
                    ->all(),

                'categories' => CourseCategory::query()
                    ->withCount(['courses as total' => fn (Builder $q) => $q->published()])
                    ->pluck('total', 'slug')
                    ->all(),
            ]
        );
    }

    #[Computed]
    public function categories()
    {
        return Cache::remember(
            'catalogue.categories',
            now()->addHour(),
            fn () => CourseCategory::active()->ordered()->withPublishedCourseCount()->get()
        );
    }

    #[Computed]
    public function cityOptions()
    {
        return Cache::remember(
            'catalogue.cities',
            now()->addHours(6),
            fn () => City::active()->hasUpcomingSessions()->orderBy('name')->get(['id', 'name', 'slug'])
        );
    }

    #[Computed]
    public function activeFilters(): array
    {
        return array_filter([
            'search'      => $this->search,
            'category'    => $this->category,
            'subcategory' => $this->subcategory,
            'modes'       => $this->modes,
            'levels'      => $this->levels,
            'cities'      => $this->cities,
        ]);
    }

    /**
     * Robots directive for this URL.
     *
     * One facet is a genuine landing page and should be indexed. Two or more
     * is a filter state — followed, so the crawler reaches the courses, but
     * not indexed, because 524 x 12 x 26 x 3 x 3 combinations of near
     * duplicate pages is how a catalogue earns a manual action.
     */
    #[Computed]
    public function robots(): string
    {
        return app(SeoService::class)->facetedRobots(
            count($this->activeFilters()),
            (int) $this->getPage()
        );
    }

    public function render(): View
    {
        return view('livewire.public.course-catalogue')
            ->title($this->pageTitle());
    }

    private function pageTitle(): string
    {
        if ($this->category) {
            $name = $this->categories->firstWhere('slug', $this->category)?->name;

            return $name ? "{$name} Training Courses | Academia" : 'Training Catalogue | Academia';
        }

        return 'Training Catalogue — 500+ Professional Courses | Academia';
    }
}
