<x-layouts.app title="About Academia Training Solutions"
               description="A European training academy built by practitioners. Small groups, practising experts, and quality standards we put in writing.">
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap grid gap-10 lg:grid-cols-[1.2fr_.8fr] lg:items-end">
            <div>
                <p class="eyebrow !text-gold-400">About Academia</p>
                <h1 class="mt-3 text-white">A European training academy built by practitioners</h1>
                <p class="lede mt-4 text-white/80">
                    Academia Training Solutions is a trade name of {{ config('academia.legal_entity') }}.
                    We exist because most professional training is written by people who no longer do
                    the work. We build every programme with someone currently practising the subject,
                    deliver it in small groups, and measure whether anything changed afterwards.
                </p>
            </div>
            <div class="rounded-card border border-white/15 bg-white/5 p-6">
                <h2 class="text-base text-white">What we will not do</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-white/85">
                    @foreach ([
                        'Sell you a course we do not think you need',
                        'Put 40 people in a room and call it a workshop',
                        'Hand over slides without a working toolkit',
                        'Use anyone who has never done the job they teach',
                    ] as $line)
                        <li class="flex gap-2">
                            <span class="text-orange-300">×</span>
                            <span>{{ $line }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <div class="wrap max-w-[760px] py-14">
        <h2>How we choose experts</h2>
        <p class="mt-3 leading-relaxed text-sand-700">
            Every expert has at least ten years in the field they teach and is still practising —
            not a career trainer who last did the job a decade ago. Each is reference-checked and
            audition-tested before they take a cohort.
        </p>
        <p class="mt-4 leading-relaxed text-sand-700">
            We confirm the named expert with your joining instructions rather than on the website.
            That is deliberate: it protects the associate from being approached directly, and it
            means we match the individual to your cohort rather than to a marketing page.
        </p>

        <h2 class="mt-10">Our quality standards</h2>
        <p class="mt-3 text-sand-700">
            We do not claim certifications we do not hold. These are the operating standards every
            Academia programme is held to — verifiable, and written into our terms.
        </p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            @foreach ([
                ['Delivery', 'Maximum 14 per class', 'Classroom groups are capped at 14 and virtual classrooms at 12, so every participant gets airtime and feedback.'],
                ['Expertise', '10+ years, still practising', 'Every expert has more than a decade in the field they teach and is vetted through references and a teaching audition.'],
                ['Outcome', 'Satisfaction guarantee', 'If a programme does not meet the objectives we agreed in writing, you attend again at no cost.'],
                ['Data', 'EU-only processing', 'Participant data is hosted and processed inside the EU under a signed DPA, and never used for marketing without consent.'],
            ] as [$chip, $heading, $body])
                <div class="card p-5">
                    <span class="chip-green">{{ $chip }}</span>
                    <h3 class="mt-3 text-base">{{ $heading }}</h3>
                    <p class="mt-2 text-sm text-sand-600">{{ $body }}</p>
                </div>
            @endforeach
        </div>
        <p class="mt-4 text-xs text-sand-500">
            Awarding-body partnerships are listed on the relevant course pages once each accreditation
            is formally in place.
        </p>

        <h2 class="mt-10">What we will not do</h2>
        <ul class="mt-4 space-y-3 text-sand-700">
            @foreach ([
                'Publish a rating we generated ourselves. No score appears anywhere on this site until an independent review platform is connected.',
                'Claim an accreditation we do not hold. Where a course prepares you for a third-party certification we say so explicitly and name the scheme owner.',
                'Raise a list price to make a discount look larger. The crossed-out number is a price we actually charge.',
                'Invent a testimonial. Anything illustrative on this site is labelled as illustrative.',
            ] as $commitment)
                <li class="flex gap-3">
                    <svg class="mt-1 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    <span>{{ $commitment }}</span>
                </li>
            @endforeach
        </ul>
    </div>
    <x-cta-band />
</x-layouts.app>
