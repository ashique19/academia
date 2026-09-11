<x-layouts.app>

    {{-- HERO ------------------------------------------------------------- --}}
    <section class="relative overflow-hidden bg-sand-900 text-white">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0
             bg-[radial-gradient(1100px_620px_at_78%_8%,rgba(224,165,38,.24),transparent_62%),radial-gradient(880px_560px_at_8%_92%,rgba(177,71,5,.34),transparent_60%),radial-gradient(700px_500px_at_52%_118%,rgba(46,107,79,.30),transparent_62%)]"></div>

        <div class="wrap relative grid items-center gap-12 py-16 lg:grid-cols-[1.06fr_.94fr] lg:py-24">
            <div>
                @if ($promotion)
                    <span class="chip mb-5 bg-white/10 text-white">
                        {{ $promotion->name }} — {{ $promotion->percentage }}% off
                    </span>
                @endif

                {{-- One h1. Three visual lines, one element. --}}
                <h1 class="text-white">
                    Transform Skills. Empower Careers.
                    <em class="not-italic text-gold-400">Build Future-Ready Teams.</em>
                </h1>

                <p class="lede mt-5 max-w-[53ch] text-white/80">
                    Professional online, onsite and classroom training delivered by experienced
                    practitioners across Europe. Over {{ $stats['courses'] }} courses in business,
                    finance, technology, supply chain, HR and compliance.
                </p>

                {{-- The only Livewire component in the hero. It redirects to a
                     real URL rather than mutating state, so a search is
                     shareable and shows up in analytics. --}}
                <div class="mt-8">
                    <livewire:public.course-search />
                </div>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('courses.index') }}" class="btn-primary btn-lg" wire:navigate>
                        Browse {{ $stats['courses'] }}+ Courses
                    </a>
                    <a href="{{ route('corporate') }}#proposal" class="btn-gold btn-lg" wire:navigate>
                        Request Corporate Training
                    </a>
                    <a href="{{ route('schedule') }}" class="btn-light btn-lg" wire:navigate>
                        View Training Schedule
                    </a>
                </div>
            </div>

            <div class="hidden lg:block">
                <x-hero-art />
            </div>
        </div>

        <div class="relative border-t border-white/10 bg-black/20">
            <div class="wrap grid grid-cols-2 gap-6 py-6 md:grid-cols-4">
                @foreach ([
                    [$stats['courses'], 'Courses, all deliverable'],
                    ['Max 14', 'Per classroom · 12 online'],
                    ['10+ yrs', 'Every expert, still practising'],
                    [$stats['cities'], 'European cities'],
                ] as [$figure, $label])
                    <div>
                        <p class="font-display text-3xl font-semibold text-white">{{ $figure }}</p>
                        <p class="mt-0.5 text-[11px] font-bold uppercase tracking-wider text-white/60">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- DELIVERY MODES ---------------------------------------------------- --}}
    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">How you learn with us</p>
            <h2 class="mt-3 max-w-[22ch]">Three ways to train. One standard of quality.</h2>
            <p class="lede mt-4 max-w-[62ch]">
                Every course in our catalogue can be delivered live online, at your own offices,
                or in a public classroom in a European city near you. Same expert, same materials,
                same certificate.
            </p>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['Online Training', 'online', 'Live online', 'Instructor-led virtual classrooms and self-paced modules. Join from anywhere in Europe with no travel cost.', ['Live interaction and breakout rooms', 'Sessions recorded for 90 days', 'Digital certificate on completion'], 'green'],
                    ['Onsite Corporate Training', 'corporate', 'In-company', 'Your expert travels to you. Content built around your systems, your data and your team\'s real work.', ['Fully customised curriculum', 'Pre-training skills assessment', 'Cost-effective from six participants'], 'orange'],
                    ['Classroom Training', 'locations', 'Public courses', 'Scheduled public courses in European cities. Learn alongside professionals from other organisations.', ['Central, well-connected venues', 'Small groups, maximum 14 people', 'Lunch and materials included'], 'gold'],
                ] as [$heading, $routeName, $chip, $body, $points, $tone])
                    <article class="card card-hover flex flex-col p-6">
                        <span @class([
                            'chip w-fit',
                            'chip-green'  => $tone === 'green',
                            'chip-orange' => $tone === 'orange',
                            'chip-gold'   => $tone === 'gold',
                        ])>{{ $chip }}</span>

                        <h3 class="mt-4">{{ $heading }}</h3>
                        <p class="mt-2 text-sm text-sand-600">{{ $body }}</p>

                        <ul class="mt-4 space-y-2 text-sm">
                            @foreach ($points as $point)
                                <li class="flex gap-2">
                                    <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                    <span>{{ $point }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route($routeName) }}" wire:navigate
                           class="mt-5 text-sm font-semibold text-orange-600 hover:underline">
                            Explore {{ strtolower($heading) }} →
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CATEGORIES -------------------------------------------------------- --}}
    <section class="bg-brand-cream py-20">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">{{ $stats['courses'] }} training topics</p>
                    <h2 class="mt-3">Find training by subject area</h2>
                </div>
                <a href="{{ route('courses.index') }}" class="btn-ghost" wire:navigate>View the full catalogue</a>
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <a href="{{ route('courses.category', $category) }}" wire:navigate
                       class="card card-hover flex flex-col p-6">
                        <h3 class="text-lg">{{ $category->name }}</h3>
                        <p class="mt-2 line-clamp-2 text-sm text-sand-600">{{ $category->summary }}</p>
                        <span class="mt-4 text-sm font-semibold text-orange-600">
                            {{ $category->published_courses_count }} courses →
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- POPULAR COURSES --------------------------------------------------- --}}
    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">Booking fast</p>
            <h2 class="mt-3">Courses professionals are booking this quarter</h2>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (($featured->isNotEmpty() ? $featured : $popular) as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- UPCOMING SCHEDULE ------------------------------------------------- --}}
    <section class="bg-sand-50 py-20">
        <div class="wrap">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="eyebrow">Confirmed dates</p>
                    <h2 class="mt-3">Starting soon</h2>
                </div>
                <a href="{{ route('schedule') }}" class="btn-ghost" wire:navigate>See the full schedule</a>
            </div>

            <div class="mt-8 grid gap-3">
                @foreach ($upcoming as $session)
                    <x-session-row :session="$session" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- CORPORATE --------------------------------------------------------- --}}
    <section class="bg-sand-900 py-20 text-white">
        <div class="wrap grid items-center gap-10 lg:grid-cols-[1.2fr_.8fr]">
            <div>
                <p class="eyebrow !text-gold-400">Corporate &amp; in-company</p>
                <h2 class="mt-3 text-white">Develop your workforce with practical, industry-relevant training</h2>
                <p class="lede mt-4 text-white/75">
                    From a single team workshop to a multi-country capability programme. We assess
                    the gap, design the curriculum around your systems and sector, deliver it
                    wherever your people are, and report on what changed.
                </p>
                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('corporate') }}#proposal" class="btn-gold btn-lg" wire:navigate>
                        Request a proposal
                    </a>
                    <a href="{{ route('offers') }}" class="btn-light btn-lg" wire:navigate>
                        See group rates
                    </a>
                </div>
            </div>

            <div class="rounded-card border border-white/15 bg-white/5 p-6">
                <ul class="space-y-3 text-sm text-white/85">
                    @foreach ([
                        'Proposal within two working days, fixed price',
                        'Delivered at your offices, live online, or in a private classroom',
                        'Delivered in English, localised to your market',
                        'One invoice, one point of contact, one capability report',
                    ] as $point)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- CITIES ------------------------------------------------------------ --}}
    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">Classroom training across Europe</p>
            <h2 class="mt-3">Public courses in {{ $stats['cities'] }} European cities</h2>

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($cities as $city)
                    <a href="{{ route('locations.city', [$city->country, $city]) }}" wire:navigate
                       class="card card-hover p-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-sand-500">{{ $city->country->name }}</p>
                        <h3 class="mt-1 text-lg">{{ $city->name }}</h3>
                        <p class="mt-2 text-sm text-sand-600">
                            {{ $city->upcoming_sessions_count }} upcoming
                            {{ \Illuminate\Support\Str::plural('date', $city->upcoming_sessions_count) }}
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS ------------------------------------------------------ --}}
    @if ($testimonials->isNotEmpty())
        <section class="bg-brand-cream py-20">
            <div class="wrap">
                <p class="eyebrow">What participants tell us</p>
                <h2 class="mt-3">The feedback we design for</h2>
                <p class="lede mt-4 max-w-[70ch]">
                    We do not publish attributed quotes, because we will not put a name and a face to
                    words a participant did not write. What we can publish is the standard every
                    programme is built to meet — and an independent review feed once participants
                    have submitted their own.
                </p>

                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <figure @class([
                            'card p-6',
                            'border-dashed' => $testimonial->is_illustrative,
                        ])>
                            @if ($testimonial->is_illustrative)
                                {{-- Load-bearing. An illustrative quote without
                                     this marker is indistinguishable from a
                                     fabricated customer review. --}}
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

                <p class="mt-6 max-w-[80ch] text-xs text-sand-500">
                    Academia publishes no rating of its own. When an independent review platform is
                    connected, verified reviews and a verified score appear here — and only then does
                    <code>aggregateRating</code> schema begin to emit.
                </p>
            </div>
        </section>
    @endif

    <x-cta-band />

</x-layouts.app>
