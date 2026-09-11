<div>
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <h1 class="text-white">Training schedule</h1>
            <p class="lede mt-3 max-w-[70ch] text-white/75">
                Confirmed dates with real seat counts, shown before you enquire. Filter by subject,
                city, delivery method or date.
            </p>
        </div>
    </section>

    <div class="wrap py-10">
        <div class="flex flex-wrap items-center gap-3 border-b border-sand-200 pb-5">
            <div class="inline-flex rounded-lg border border-sand-300 p-0.5">
                @foreach (['list' => 'List', 'calendar' => 'Calendar'] as $value => $label)
                    <button type="button" wire:click="setView('{{ $value }}')"
                            @class([
                                'rounded-md px-3.5 py-1.5 text-sm font-semibold transition',
                                'bg-sand-900 text-white' => $viewMode === $value,
                                'text-sand-600 hover:bg-sand-50' => $viewMode !== $value,
                            ])>{{ $label }}</button>
                @endforeach
            </div>

            <select wire:model.live="category" class="select w-auto">
                <option value="">All subjects</option>
                @foreach ($this->categories as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="mode" class="select w-auto">
                <option value="">Any delivery method</option>
                @foreach ($this->modes as $deliveryMode)
                    <option value="{{ $deliveryMode->slug }}">{{ $deliveryMode->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="country" class="select w-auto">
                <option value="">Any country</option>
                @foreach ($this->countries as $countryOption)
                    <option value="{{ $countryOption->slug }}">{{ $countryOption->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="city" class="select w-auto">
                <option value="">Any city</option>
                @foreach ($this->cities as $cityOption)
                    <option value="{{ $cityOption->slug }}">{{ $cityOption->name }}</option>
                @endforeach
            </select>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model.live="availableOnly"
                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                Seats available only
            </label>

            <button type="button" wire:click="resetFilters"
                    class="ml-auto text-sm font-semibold text-orange-600 hover:underline">Reset</button>
        </div>

        <div wire:loading.class="opacity-50" class="mt-6 transition-opacity">
            @if ($viewMode === 'calendar')
                @php $calendar = $this->calendar; @endphp

                <div class="flex items-center justify-between">
                    <h2 class="text-xl">{{ $calendar['month']->format('F Y') }}</h2>
                    <div class="flex gap-2">
                        <button type="button" wire:click="shiftMonth(-1)" class="btn-ghost">← Previous</button>
                        <button type="button" wire:click="shiftMonth(1)" class="btn-ghost">Next →</button>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-7 gap-px overflow-hidden rounded-card border border-sand-200 bg-sand-200 text-sm">
                    @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dayName)
                        <div class="bg-sand-50 p-2 text-center text-xs font-bold uppercase text-sand-500">{{ $dayName }}</div>
                    @endforeach

                    @foreach ($calendar['days'] as $day)
                        <div @class([
                            'min-h-[110px] bg-white p-1.5',
                            'bg-sand-50/60 text-sand-400' => ! $day['inMonth'],
                            'ring-2 ring-inset ring-orange-500' => $day['isToday'],
                        ])>
                            <span class="text-xs font-semibold">{{ $day['date']->format('j') }}</span>
                            <div class="mt-1 space-y-1">
                                @foreach ($day['sessions']->take(3) as $session)
                                    <a href="{{ route('courses.show', $session->course) }}" wire:navigate
                                       wire:key="cal-{{ $session->id }}"
                                       class="block truncate rounded bg-orange-50 px-1.5 py-0.5 text-[11px] text-orange-700 hover:bg-orange-100"
                                       title="{{ $session->course->display_title }}">
                                        {{ $session->course->display_title }}
                                    </a>
                                @endforeach
                                @if ($day['sessions']->count() > 3)
                                    <span class="block px-1.5 text-[11px] text-sand-500">
                                        +{{ $day['sessions']->count() - 3 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-sand-500">
                    Showing {{ number_format($this->sessions->total()) }} sessions
                    @if (! $from && ! $to)
                        in the next {{ config('academia.schedule.default_window_days') }} days
                    @endif
                </p>

                <div class="mt-4 grid gap-3">
                    @forelse ($this->sessions as $session)
                        <x-session-row :session="$session" wire:key="row-{{ $session->id }}" />
                    @empty
                        <div class="card p-10 text-center">
                            <h3>No sessions match those filters</h3>
                            <p class="mt-2 text-sand-600">Try widening the date range or removing a filter.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">{{ $this->sessions->links() }}</div>
            @endif
        </div>
    </div>
</div>
