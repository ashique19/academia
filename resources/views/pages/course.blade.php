<x-layouts.app :title="$course->display_title . ' | Academia Training Solutions'"
               :description="$course->summary">

    @push('schema')
        <script type="application/ld+json">{!! json_encode(app(\App\Domain\Content\Services\CourseSchemaBuilder::class)->build($course, $price), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

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
                    <ul class="space-y-2.5">
                        @foreach ([
                            'Maximum ' . $course->max_participants . ' participants',
                            $course->certificate ?: 'Certificate on completion',
                            'Materials, toolkit and lunch included',
                            'Free cancellation up to 14 days before',
                        ] as $point)
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

    <div class="wrap grid gap-10 py-12 lg:grid-cols-[1fr_360px]">
        <div>
            {{-- CERTIFICATION SCHEME NOTICE --------------------------------
                 Rendered automatically whenever the course is matched to a
                 trademarked scheme Academia does not hold a licence for.
                 Flip the scheme to `accredited` and this becomes a badge. --}}
            @if ($course->scheme?->isIndependent())
                <div class="card border-gold-200 bg-gold-50 p-6">
                    <h2 class="text-lg">Independent {{ $course->scheme->name }} exam preparation</h2>
                    <p class="mt-2 text-sm text-sand-700">{{ $course->scheme->trademarkNotice() }}</p>
                    <p class="mt-3 text-xs text-sand-500">{{ $course->scheme->trademarkAttribution() }}</p>
                </div>
            @endif

            <div class="prose-academia mt-8">
                <h2>About this course</h2>
                <div class="mt-3 text-sand-700 leading-relaxed">{!! $course->description !!}</div>
            </div>

            @if (filled($course->learning_objectives))
                <h2 class="mt-10">What you will be able to do</h2>
                <ul class="mt-4 grid gap-2.5 sm:grid-cols-2">
                    @foreach ($course->learning_objectives as $objective)
                        <li class="flex gap-2.5 text-sm">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $objective }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($course->modules->isNotEmpty())
                <h2 class="mt-10">Programme</h2>
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
            @endif

            @if ($course->target_audience)
                <h2 class="mt-10">Who should attend</h2>
                <p class="mt-3 text-sand-700">{{ $course->target_audience }}</p>
            @endif

            <h2 class="mt-10">Prerequisites</h2>
            <p class="mt-3 text-sand-700">{{ $course->prerequisites ?: 'None. This course starts from first principles.' }}</p>

            {{-- YOUR EXPERT ------------------------------------------------
                 Anonymised by default. See spec §3.1 — the site sells the
                 STANDARD, and releases the named profile with the joining
                 instructions. Flip trainers.is_public to change this. --}}
            <h2 class="mt-10">Your expert</h2>
            <div class="card mt-4 p-6">
                <p class="text-sand-700">
                    A subject-matter expert with 10+ years in this field, still practising,
                    reference-checked and matched to your cohort. We confirm the named expert
                    with your joining instructions.
                </p>
            </div>

            @if ($course->availableCities->isNotEmpty())
                <h2 class="mt-10">Where this course runs</h2>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($course->availableCities as $city)
                        <a href="{{ route('courses.city', [$course, $city]) }}" wire:navigate class="chip hover:bg-sand-200">
                            {{ $city->name }}
                        </a>
                    @endforeach
                </div>
            @endif
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
