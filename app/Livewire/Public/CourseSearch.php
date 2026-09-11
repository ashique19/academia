<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Shared\Models\City;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The hero search.
 *
 * Submits by REDIRECTING to a catalogue URL rather than mutating state in
 * place. A search that changes what is on screen without changing the address
 * bar is unshareable, breaks the back button, and is invisible to analytics.
 */
class CourseSearch extends Component
{
    public string $query = '';
    public string $category = '';
    public string $city = '';

    public function search(): void
    {
        $this->validate(['query' => ['nullable', 'string', 'max:120']]);

        $this->redirectRoute('courses.index', array_filter([
            'q'        => trim($this->query),
            'category' => $this->category,
            'city'     => $this->city ? [$this->city] : null,
        ]), navigate: true);
    }

    #[Computed]
    public function categories()
    {
        return cache()->remember('search.categories', now()->addHour(), fn () =>
            CourseCategory::active()->ordered()->get(['id', 'name', 'slug']));
    }

    #[Computed]
    public function cities()
    {
        return cache()->remember('search.cities', now()->addHours(6), fn () =>
            City::active()->hasUpcomingSessions()->orderBy('name')->get(['id', 'name', 'slug']));
    }

    public function render(): View
    {
        return view('livewire.public.course-search');
    }
}
