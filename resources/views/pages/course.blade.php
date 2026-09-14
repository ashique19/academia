<x-layouts.app :title="$course->display_title . ' | Academia Training Solutions'"
               :description="$course->summary">

    @push('schema')
        <script type="application/ld+json">{!! json_encode(app(\App\Domain\Content\Services\CourseSchemaBuilder::class)->build($course, $price), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    @php
        $scheme = $course->scheme;
        $hasExamDetails = $scheme && collect([
            $scheme->exam_questions,
            $scheme->exam_format,
            $scheme->exam_pass_mark,
            $scheme->exam_duration,
            $scheme->exam_book,
            $scheme->pathway,
        ])->contains(fn ($value) => filled($value));

        $audienceItems = filled($course->target_audience)
            ? collect(preg_split('/\s*\|\s*/', (string) $course->target_audience))->filter()->values()
            : collect();

        $includeItems = collect($course->includes ?? [])->filter()->values();
        if ($includeItems->isEmpty()) {
            $includeItems = collect([
                'Maximum '.$course->max_participants.' participants',
                $course->certificate ?: 'Certificate on completion',
                'Materials, toolkit and lunch included',
                'Free cancellation up to 14 days before',
            ]);
        }

        $pathwaySteps = filled($scheme?->pathway)
            ? collect(preg_split('/\s*\|\s*/', (string) $scheme->pathway))->filter()->values()
            : collect();

        $tabs = collect([
            ['id' => 'overview', 'label' => 'Overview'],
            ['id' => 'objectives', 'label' => 'Objectives', 'show' => filled($course->learning_objectives)],
            ['id' => 'programme', 'label' => 'Programme', 'show' => $course->modules->isNotEmpty()],
            ['id' => 'audience', 'label' => 'Who should attend'],
            ['id' => 'exam', 'label' => 'The exam', 'show' => $hasExamDetails],
            ['id' => 'expert', 'label' => 'Your expert'],
            ['id' => 'reviews', 'label' => 'Reviews', 'show' => $reviews->isNotEmpty()],
            ['id' => 'faq', 'label' => 'FAQ', 'show' => $faqs->isNotEmpty()],
        ])->filter(fn (array $tab) => ($tab['show'] ?? true))->values();

        $defaultTab = $tabs->first()['id'] ?? 'overview';
    @endphp

    <section class="bg-sand-900 py-12 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-white">Home</a>
                <span class="mx-1">›</span>
                <a href="{{ route('courses.index') }}" wire:navigate class="hover:text-white">Catalogue</a>
                <span class="mx-1">›</span>
                <a href="{{ route('courses.category', $course->subcategory->category) }}" wire:navigate class="hover:text-white">
                    {{ $course->subcategory->category->name }}
                </a>
            </nav>

            <div class="mt-4 grid gap-10 lg:grid-cols-[1.5fr_.8fr]">
                <div>
                    <span class="chip-gold">{{ $course->subcategory->name }}</span>
                    <h1 class="mt-3 text-white">{{ $course->display_title }}</h1>
                    <p class="lede mt-4 text-white/80">{{ $course->summary }}</p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <span class="chip bg-white/10 text-white">
                            {{ rtrim(rtrim(number_format((float) $course->duration_days, 1), '0'), '.') }}
                            {{ (float) $course->duration_days === 1.0 ? 'day' : 'days' }}
                            @if ($course->duration_hours) · {{ $course->duration_hours }} hours @endif
                        </span>
                        <span class="chip bg-white/10 text-white">{{ $course->level->label() }}</span>
                        <span class="chip bg-white/10 text-white">{{ $course->schedules->count() }} upcoming dates</span>
                        @if ($price->hasDiscount())
                            <span class="chip bg-gradient-to-r from-orange-500 to-gold-400 text-white">
                                {{ $price->discountPercent }}% off
                            </span>
                        @endif
                    </div>
                </div>

                <div class="rounded-card border border-white/15 bg-white/5 p-6 text-sm text-white/85">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-white/60">What's included</p>
                    <ul class="mt-3 space-y-2.5">
                        @foreach ($includeItems as $point)
                            <li class="flex gap-3">
                                <svg class="mt-0.5 h-4 w-4 flex-none text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <div
        class="wrap grid gap-10 py-12 lg:grid-cols-[1fr_360px]"
        x-data="{
            tab: @js($defaultTab),
            tabs: @js($tabs->pluck('id')->all()),
            init() {
                const hash = (window.location.hash || '').replace(/^#/, '');
                if (hash && this.tabs.includes(hash)) {
                    this.tab = hash;
                }
            },
            select(id) {
                this.tab = id;
                history.replaceState(null, '', '#' + id);
                this.$nextTick(() => {
                    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            },
        }"
    >
        <div>
            {{-- CERTIFICATION SCHEME NOTICE --------------------------------
                 Rendered automatically whenever the course is matched to a
                 trademarked scheme Academia does not hold a licence for.
                 Flip the scheme to `accredited` and this becomes a badge. --}}
            @if ($scheme?->isIndependent())
                <div class="card border-gold-200 bg-gold-50 p-6">
                    <h2 class="text-lg">Independent {{ $scheme->name }} exam preparation</h2>
                    <p class="mt-2 text-sm text-sand-700">{{ $scheme->trademarkNotice() }}</p>
                    <p class="mt-3 text-xs text-sand-500">{{ $scheme->trademarkAttribution() }}</p>
                </div>
            @endif

            <div
                class="sticky top-16 z-20 -mx-5 mt-8 border-y border-sand-200 bg-white/95 px-5 backdrop-blur sm:-mx-0 sm:rounded-card sm:border sm:px-2"
                role="tablist"
                aria-label="Course sections"
            >
                <div class="flex gap-1 overflow-x-auto py-2">
                    @foreach ($tabs as $tab)
                        <button
                            type="button"
                            role="tab"
                            id="tab-{{ $tab['id'] }}"
                            :aria-selected="tab === '{{ $tab['id'] }}'"
                            :tabindex="tab === '{{ $tab['id'] }}' ? 0 : -1"
                            @click="select('{{ $tab['id'] }}')"
                            :class="tab === '{{ $tab['id'] }}'
                                ? 'bg-sand-900 text-white'
                                : 'text-sand-600 hover:bg-sand-100 hover:text-sand-900'"
                            class="shrink-0 rounded-pill px-3.5 py-2 text-sm font-semibold transition"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="mt-8">
                <div
                    x-show="tab === 'overview'"
                    x-cloak
                    role="tabpanel"
                    aria-labelledby="tab-overview"
                    id="overview"
                >
                    <div class="prose-academia">
                        <h2>About this course</h2>
                        <div class="mt-3 text-sand-700 leading-relaxed">{!! $course->description !!}</div>
                    </div>

                    @if (collect($course->includes ?? [])->filter()->isNotEmpty())
                        <h3 class="mt-10 text-base font-semibold">Course includes</h3>
                        <ul class="mt-4 grid gap-2.5 sm:grid-cols-2">
                            @foreach ($course->includes as $item)
                                @continue(! filled($item))
                                <li class="flex gap-2.5 text-sm">
                                    <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($course->availableCities->isNotEmpty())
                        <h3 class="mt-10 text-base font-semibold">Where this course runs</h3>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($course->availableCities as $city)
                                <a href="{{ route('courses.city', [$course, $city]) }}" wire:navigate class="chip hover:bg-sand-200">
                                    {{ $city->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                @if (filled($course->learning_objectives))
                    <div
                        x-show="tab === 'objectives'"
                        x-cloak
                        role="tabpanel"
                        aria-labelledby="tab-objectives"
                        id="objectives"
                    >
                        <h2>What you will be able to do</h2>
                        <ul class="mt-4 grid gap-2.5 sm:grid-cols-2">
                            @foreach ($course->learning_objectives as $objective)
                                <li class="flex gap-2.5 text-sm">
                                    <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span>{{ $objective }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($course->modules->isNotEmpty())
                    <div
                        x-show="tab === 'programme'"
                        x-cloak
                        role="tabpanel"
                        aria-labelledby="tab-programme"
                        id="programme"
                    >
                        <h2>Programme</h2>
                        <p class="mt-1.5 text-sm text-sand-500">{{ $course->modules->count() }} modules</p>

                        <div class="mt-4 divide-y divide-sand-200 overflow-hidden rounded-card border border-sand-200">
                            @foreach ($course->modules as $index => $module)
                                <details class="group" @if($index === 0) open @endif>
                                    <summary class="flex cursor-pointer items-center gap-3 p-4 hover:bg-sand-50">
                                        <span class="flex h-7 w-7 flex-none items-center justify-center rounded-full bg-sand-100 text-xs font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="flex-1 font-semibold">{{ $module->title }}</span>
                                        <svg class="h-4 w-4 text-sand-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                                    </summary>
                                    @if (filled($module->bullets))
                                        <ul class="space-y-1.5 border-t border-sand-100 bg-sand-50/50 px-4 py-3 pl-14 text-sm text-sand-600">
                                            @foreach ($module->bullets as $bullet)
                                                <li class="list-disc">{{ $bullet }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div
                    x-show="tab === 'audience'"
                    x-cloak
                    role="tabpanel"
                    aria-labelledby="tab-audience"
                    id="audience"
                >
                    <h2>Who should attend</h2>
                    @if ($audienceItems->isNotEmpty())
                        <ul class="mt-4 space-y-2.5">
                            @foreach ($audienceItems as $item)
                                <li class="flex gap-2.5 text-sm text-sand-700">
                                    <svg class="mt-0.5 h-4 w-4 flex-none text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-sand-700">Open to anyone building capability in this subject.</p>
                    @endif

                    <h3 class="mt-10 text-base font-semibold">Prerequisites</h3>
                    <p class="mt-3 text-sand-700">{{ $course->prerequisites ?: 'None. This course starts from first principles.' }}</p>
                </div>

                @if ($hasExamDetails)
                    <div
                        x-show="tab === 'exam'"
                        x-cloak
                        role="tabpanel"
                        aria-labelledby="tab-exam"
                        id="exam"
                    >
                        <h2>The exam</h2>
                        <p class="mt-2 text-sm text-sand-600">
                            Official {{ $scheme->name }} exam details. Academia prepares you for the exam;
                            the exam itself is booked separately with {{ $scheme->owner }}.
                        </p>

                        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                            @foreach ([
                                'Questions' => $scheme->exam_questions,
                                'Format' => $scheme->exam_format,
                                'Pass mark' => $scheme->exam_pass_mark,
                                'Duration' => $scheme->exam_duration,
                                'Open / closed book' => $scheme->exam_book,
                            ] as $label => $value)
                                @continue(! filled($value))
                                <div class="rounded-card border border-sand-200 bg-sand-50/60 p-4">
                                    <dt class="text-[11px] font-bold uppercase tracking-widest text-sand-500">{{ $label }}</dt>
                                    <dd class="mt-1.5 font-semibold text-sand-900">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        @if ($pathwaySteps->isNotEmpty())
                            <h3 class="mt-10 text-base font-semibold">Certification pathway</h3>
                            <ol class="mt-4 space-y-3">
                                @foreach ($pathwaySteps as $index => $step)
                                    <li class="flex items-center gap-3 text-sm">
                                        <span class="flex h-7 w-7 flex-none items-center justify-center rounded-full bg-sand-900 text-xs font-bold text-white">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="font-semibold">{{ $step }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @endif

                <div
                    x-show="tab === 'expert'"
                    x-cloak
                    role="tabpanel"
                    aria-labelledby="tab-expert"
                    id="expert"
                >
                    {{-- YOUR EXPERT ------------------------------------------------
                         Anonymised by default. See spec §3.1 — the site sells the
                         STANDARD, and releases the named profile with the joining
                         instructions. Flip trainers.is_public to change this. --}}
                    <h2>Your expert</h2>
                    <div class="card mt-4 p-6">
                        <p class="text-sand-700">
                            A subject-matter expert with 10+ years in this field, still practising,
                            reference-checked and matched to your cohort. We confirm the named expert
                            with your joining instructions.
                        </p>
                    </div>
                </div>

                @if ($reviews->isNotEmpty())
                    <div
                        x-show="tab === 'reviews'"
                        x-cloak
                        role="tabpanel"
                        aria-labelledby="tab-reviews"
                        id="reviews"
                    >
                        <h2>Reviews</h2>
                        <div class="mt-6 grid gap-5">
                            @foreach ($reviews as $testimonial)
                                <figure @class([
                                    'card p-6',
                                    'border-dashed' => $testimonial->is_illustrative,
                                ])>
                                    @if ($testimonial->is_illustrative)
                                        <span class="sample-flag">Illustrative</span>
                                    @endif
                                    <blockquote class="mt-3 text-sm leading-relaxed">
                                        “{{ $testimonial->quote }}”
                                    </blockquote>
                                    <figcaption class="mt-4 text-xs text-sand-500">
                                        {{ $testimonial->attribution }}
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($faqs->isNotEmpty())
                    <div
                        x-show="tab === 'faq'"
                        x-cloak
                        role="tabpanel"
                        aria-labelledby="tab-faq"
                        id="faq"
                    >
                        <h2>Frequently asked questions</h2>
                        @if ($faqsAreFallback ?? false)
                            <p class="mt-2 text-sm text-sand-500">
                                Common questions about booking and delivery. Course-specific FAQs appear here when published.
                            </p>
                        @endif
                        <div class="mt-5 divide-y divide-sand-200 overflow-hidden rounded-card border border-sand-200">
                            @foreach ($faqs as $faq)
                                <details class="group" @if ($loop->first) open @endif>
                                    <summary class="flex cursor-pointer items-center gap-3 p-5 font-semibold hover:bg-sand-50">
                                        <span class="flex-1">{{ $faq->question }}</span>
                                        <svg class="h-4 w-4 text-sand-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                                    </summary>
                                    <p class="border-t border-sand-100 bg-sand-50/50 p-5 text-sm text-sand-600">{{ $faq->answer }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- BOOKING SIDEBAR ---------------------------------------------- --}}
        <aside class="lg:sticky lg:top-24 lg:self-start" id="book">
            <div class="card overflow-hidden">
                <div class="bg-sand-900 p-6 text-white">
                    <p class="text-[11px] font-bold uppercase tracking-widest text-white/70">Book your place</p>

                    @if ($price->requiresQuote())
                        <p class="mt-2 font-display text-2xl font-semibold">Request a quote</p>
                    @elseif ($price->hasDiscount())
                        <p class="mt-1">
                            <span class="font-display text-3xl font-semibold">€{{ number_format($price->finalPriceCents / 100, 0, ',', '.') }}</span>
                            <span class="ml-2 font-semibold text-white/60 line-through">€{{ number_format($price->referencePriceCents() / 100, 0, ',', '.') }}</span>
                        </p>
                        @php $campaign = $price->components->firstWhere('type', 'campaign'); @endphp
                        @if ($campaign)
                            <p class="mt-1 text-xs text-white/85">
                                {{ $campaign->percentage }}% off with code <strong>{{ $campaign->code }}</strong> — {{ $campaign->label }}
                            </p>
                        @endif
                    @else
                        <p class="mt-1 font-display text-3xl font-semibold">€{{ number_format($price->listPriceCents / 100, 0, ',', '.') }}</p>
                    @endif

                    <p class="mt-1 text-xs text-white/70">
                        per person, excluding VAT · materials, lunch and certificate included
                    </p>
                </div>

                <div class="p-5">
                    <livewire:public.registration-form :course="$course" />
                </div>
            </div>

            @if ($course->has_self_paced)
                <div class="card mt-5 border-green-200 bg-green-50 p-5">
                    <span class="chip-green">Also available self-paced</span>
                    <h3 class="mt-3 text-base">Learn this on your own schedule</h3>
                    <p class="mt-1.5 text-sm text-sand-600">
                        Same material as video modules, exercises and knowledge checks.
                        12 months access, tutor questions answered within one working day.
                    </p>
                    <p class="mt-3 text-xl font-bold">€{{ number_format($course->self_paced_price_cents / 100, 0, ',', '.') }}</p>
                </div>
            @endif
        </aside>
    </div>

    @if ($related->isNotEmpty())
        <section class="bg-sand-50 py-16">
            <div class="wrap">
                <h2>Learners also viewed</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($related as $relatedCourse)
                        <x-course-card :course="$relatedCourse" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-cta-band />
</x-layouts.app>
