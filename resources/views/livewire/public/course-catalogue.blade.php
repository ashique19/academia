<div>
    {{-- The robots directive is computed from the number of active facets:
         one facet is a real landing page, two or more is a filter state that
         should be followed but not indexed. --}}
    @push('schema')
        <meta name="robots" content="{{ $this->robots }}">
    @endpush

    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-white">Home</a>
                <span class="mx-1">›</span><span>Training catalogue</span>
            </nav>
            <h1 class="mt-3 text-white">Training catalogue</h1>
            <p class="lede mt-3 max-w-[70ch] text-white/75">
                Filter by subject, delivery method, level or city — every course can be delivered
                online, onsite or in a classroom.
            </p>
        </div>
    </section>

    <div class="wrap grid gap-8 py-10 lg:grid-cols-[280px_1fr]">

        {{-- FACETS --------------------------------------------------------- --}}
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-semibold">Filters</h2>
                @if ($this->activeFilters)
                    <button type="button" wire:click="resetFilters"
                            class="text-sm font-semibold text-orange-600 hover:underline">Reset</button>
                @endif
            </div>

            <div class="mt-4 space-y-6">
                <div>
                    <label class="field-label" for="facet-search">Search</label>
                    <input id="facet-search" type="search" class="input"
                           wire:model.live.debounce.300ms="search" placeholder="Course, skill or system">
                </div>

                <fieldset>
                    <legend class="field-label">Subject area</legend>
                    <select class="select" wire:model.live="category">
                        <option value="">All subjects</option>
                        @foreach ($this->categories as $category)
                            <option value="{{ $category->slug }}">
                                {{ $category->name }} ({{ $category->published_courses_count }})
                            </option>
                        @endforeach
                    </select>
                </fieldset>

                <fieldset>
                    <legend class="field-label">Level</legend>
                    <div class="space-y-1.5">
                        @foreach (\App\Domain\Catalogue\Enums\CourseLevel::cases() as $level)
                            @php $count = $this->facetCounts['levels'][$level->value] ?? 0; @endphp
                            <label @class([
                                'flex items-center gap-2 text-sm',
                                'text-sand-400' => $count === 0,
                            ])>
                                <input type="checkbox" value="{{ $level->value }}"
                                       wire:click="toggleFacet('levels', '{{ $level->value }}')"
                                       @checked(in_array($level->value, $levels, true))
                                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                                <span class="flex-1">{{ $level->label() }}</span>
                                {{-- Zero-result options are greyed, never hidden.
                                     A facet that vanishes reads as a broken site. --}}
                                <span class="text-xs text-sand-500">{{ $count }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="field-label">Delivery method</legend>
                    <div class="space-y-1.5">
                        @foreach (\App\Domain\Catalogue\Models\DeliveryMode::orderBy('sort_order')->get() as $mode)
                            @php $count = $this->facetCounts['modes'][$mode->slug] ?? 0; @endphp
                            <label @class(['flex items-center gap-2 text-sm', 'text-sand-400' => $count === 0])>
                                <input type="checkbox" value="{{ $mode->slug }}"
                                       wire:click="toggleFacet('modes', '{{ $mode->slug }}')"
                                       @checked(in_array($mode->slug, $modes, true))
                                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                                <span class="flex-1">{{ $mode->name }}</span>
                                <span class="text-xs text-sand-500">{{ $count }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset>
                    <legend class="field-label">City</legend>
                    <div class="max-h-64 space-y-1.5 overflow-y-auto pr-1">
                        @foreach ($this->cityOptions as $city)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" value="{{ $city->slug }}"
                                       wire:click="toggleFacet('cities', '{{ $city->slug }}')"
                                       @checked(in_array($city->slug, $cities, true))
                                       class="rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                                <span>{{ $city->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="card p-5">
                    <h3 class="text-sm font-semibold">Cannot find it?</h3>
                    <p class="mt-1.5 text-xs text-sand-600">
                        We build bespoke programmes on request — usually within three weeks.
                    </p>
                    <a href="{{ route('corporate') }}#proposal" wire:navigate
                       class="btn-green mt-3 w-full">Request a course</a>
                </div>
            </div>
        </aside>

        {{-- RESULTS -------------------------------------------------------- --}}
        <div>
            <div class="flex flex-wrap items-center gap-3 border-b border-sand-200 pb-4">
                <p class="text-sm">
                    <strong>{{ number_format($this->courses->total()) }}</strong>
                    {{ \Illuminate\Support\Str::plural('course', $this->courses->total()) }}
                </p>

                @foreach ($this->activeFilters as $facet => $value)
                    @foreach ((array) $value as $single)
                        <button type="button"
                                wire:click="clearFacet('{{ $facet }}', @js(is_array($value) ? $single : null))"
                                class="chip-orange hover:bg-orange-100">
                            {{ is_array($value) ? $single : $single }} ✕
                        </button>
                    @endforeach
                @endforeach

                <label class="ml-auto flex items-center gap-2 text-sm">
                    <span class="text-sand-500">Sort</span>
                    <select wire:model.live="sort" class="select w-auto py-1.5">
                        <option value="popular">Most booked</option>
                        <option value="soonest">Starting soonest</option>
                        <option value="price-asc">Price: low to high</option>
                        <option value="price-desc">Price: high to low</option>
                        <option value="newest">Newest</option>
                        <option value="az">A–Z</option>
                    </select>
                </label>
            </div>

            <div wire:loading.class="opacity-50" class="transition-opacity">
                @if ($this->courses->isEmpty())
                    <div class="card mt-8 p-10 text-center">
                        <h3>No courses match those filters</h3>
                        <p class="mt-2 text-sand-600">
                            Try removing a filter — or tell us what you are looking for and we will build it.
                        </p>
                        <a href="{{ route('corporate') }}#proposal" wire:navigate class="btn-green mt-5">
                            Request a custom programme
                        </a>
                    </div>
                @else
                    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($this->courses as $course)
                            {{-- wire:key is required on every loop item; without
                                 it, reordering corrupts the DOM diff. --}}
                            <x-course-card :course="$course" wire:key="course-{{ $course->id }}" />
                        @endforeach
                    </div>

                    <div class="mt-10">{{ $this->courses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
