<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * The public training schedule, in calendar or list form.
 *
 * Deliberately does NOT render all 1,561 sessions. The default window is 60
 * days, and the list paginates. A page that tries to show every future
 * session is slow, unreadable, and tells the visitor nothing they can act on.
 */
class ScheduleBrowser extends Component
{
    use WithPagination;

    #[Url(as: 'view', except: 'list')]
    public string $viewMode = 'list';

    #[Url(as: 'category', except: '')]
    public string $category = '';

    #[Url(as: 'mode', except: '')]
    public string $mode = '';

    #[Url(as: 'country', except: '')]
    public string $country = '';

    #[Url(as: 'city', except: '')]
    public string $city = '';

    #[Url(as: 'from', except: '')]
    public string $from = '';

    #[Url(as: 'to', except: '')]
    public string $to = '';

    #[Url(as: 'available', except: false)]
    public bool $availableOnly = false;

    /** Calendar cursor — first of the displayed month. */
    #[Url(as: 'month', except: '')]
    public string $month = '';

    public function updated(string $property): void
    {
        if (! in_array($property, ['page', 'viewMode'], true)) {
            $this->resetPage();
        }
    }

    public function setView(string $mode): void
    {
        $this->viewMode = in_array($mode, ['list', 'calendar'], true) ? $mode : 'list';
    }

    public function shiftMonth(int $delta): void
    {
        $this->month = $this->cursor()->copy()->addMonths($delta)->format('Y-m');
    }

    public function resetFilters(): void
    {
        $this->reset(['category', 'mode', 'country', 'city', 'from', 'to', 'availableOnly']);
        $this->resetPage();
    }

    private function cursor(): Carbon
    {
        return $this->month
            ? Carbon::createFromFormat('Y-m', $this->month)->startOfMonth()
            : now()->startOfMonth();
    }

    private function baseQuery(): Builder
    {
        $window = (int) config('academia.schedule.default_window_days', 60);

        return CourseSchedule::query()
            ->upcoming()
            ->publiclyVisible()
            ->withListRelations()
            ->when($this->category, fn (Builder $q, $slug) => $q->whereHas(
                'course.subcategory.category', fn (Builder $c) => $c->where('slug', $slug)
            ))
            ->when($this->mode, fn (Builder $q, $slug) => $q->whereHas(
                'deliveryMode', fn (Builder $m) => $m->where('slug', $slug)
            ))
            ->when($this->country, fn (Builder $q, $slug) => $q->inCountry($slug))
            ->when($this->city, fn (Builder $q, $slug) => $q->inCity($slug))
            ->when($this->availableOnly, fn (Builder $q) => $q->whereColumn('seats_taken', '<', 'seat_limit'))
            ->when($this->from, fn (Builder $q, $from) => $q->where('starts_at', '>=', Carbon::parse($from)))
            ->when($this->to, fn (Builder $q, $to) => $q->where('starts_at', '<=', Carbon::parse($to)->endOfDay()))
            // Without an explicit date filter, cap the horizon. Otherwise the
            // first page is dominated by sessions a year out.
            ->when(! $this->from && ! $this->to && $this->viewMode === 'list',
                fn (Builder $q) => $q->where('starts_at', '<=', now()->addDays($window)))
            ->orderBy('starts_at');
    }

    #[Computed]
    public function sessions(): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->paginate(config('academia.schedule.per_page', 50))
            ->withQueryString();
    }

    /** Sessions for the displayed month, grouped by day for the calendar grid. */
    #[Computed]
    public function calendar(): array
    {
        $start = $this->cursor();
        $end = $start->copy()->endOfMonth();

        $sessions = $this->baseQuery()
            ->reorder()
            ->whereBetween('starts_at', [$start, $end])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn (CourseSchedule $s) => $s->starts_at->format('Y-m-d'));

        $days = [];
        $cursor = $start->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $end->copy()->endOfWeek(Carbon::SUNDAY);

        while ($cursor <= $gridEnd) {
            $key = $cursor->format('Y-m-d');

            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $start->month,
                'isToday' => $cursor->isToday(),
                'sessions' => $sessions->get($key, collect()),
            ];

            $cursor->addDay();
        }

        return ['month' => $start, 'days' => $days];
    }

    #[Computed]
    public function categories()
    {
        return CourseCategory::active()->ordered()->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function modes()
    {
        return DeliveryMode::orderBy('sort_order')->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function countries()
    {
        return Country::active()->withUpcomingTraining()->orderBy('name')->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function cities()
    {
        return City::active()
            ->hasUpcomingSessions()
            ->when($this->country, fn (Builder $q, $slug) => $q->whereHas(
                'country', fn (Builder $c) => $c->where('slug', $slug)
            ))
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    public function render(): View
    {
        return view('livewire.public.schedule-browser');
    }
}
