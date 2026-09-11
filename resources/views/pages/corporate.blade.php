<x-layouts.app title="Corporate &amp; In-Company Training in Europe | Academia"
               description="Customised corporate training delivered at your offices, live online or in a private classroom anywhere in Europe. Fixed-price proposal within two working days.">

    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap grid gap-10 lg:grid-cols-[1.1fr_.9fr]">
            <div>
                <p class="eyebrow !text-gold-400">Corporate &amp; in-company training</p>
                <h1 class="mt-3 text-white">Develop your workforce with practical, industry-relevant training</h1>
                <p class="lede mt-4 text-white/80">
                    From a single team workshop to a multi-country capability programme. We assess the
                    gap, design the curriculum around your systems and sector, deliver it wherever your
                    people are, and report on what changed.
                </p>
                <ul class="mt-6 space-y-2.5 text-sm text-white/85">
                    @foreach ([
                        'Proposal within two working days, fixed price, no surprises',
                        'Delivered at your offices, live online, or in a private classroom in any European city',
                        'Delivered in English, with materials and case studies localised to your market',
                        'One invoice, one point of contact, one capability report',
                    ] as $point)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-gold-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="#proposal" class="btn-gold btn-lg mt-8">Request a proposal</a>
            </div>

            <div class="rounded-card border border-white/15 bg-white/5 p-8">
                <p class="font-display text-4xl font-semibold text-gold-400">2 days</p>
                <p class="mt-1 text-sm text-white/70">Average proposal turnaround</p>
                <hr class="my-6 border-white/10">
                <p class="font-display text-4xl font-semibold text-gold-400">2 hours</p>
                <p class="mt-1 text-sm text-white/70">
                    First response, in business hours. We measure it, and the dashboard shows a
                    breach in red.
                </p>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="wrap">
            <p class="eyebrow">What we deliver for organisations</p>
            <h2 class="mt-3">Built around your capability gap, not our catalogue</h2>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['Customised training programmes', 'We rewrite cases, data and exercises around your sector, your systems and your policies — so nothing has to be translated back into your context.'],
                    ['Employee skills assessment', 'Diagnostic before, measurement after. You get a heat map of capability by team and a defensible baseline for the training budget.'],
                    ['Onsite workshops', 'One to five days at your premises anywhere in Europe. Cost-effective from six participants and far easier to schedule.'],
                    ['Leadership programmes', 'Modular development journeys for first-time managers through to executive teams, with coaching between modules.'],
                    ['Technical &amp; systems training', 'SAP, Power BI, Excel, Python, SQL and cloud — taught in a sandbox that mirrors your own environment.'],
                    ['Digital transformation', 'Change-ready programmes for ERP rollouts, AI adoption and process automation. Trains the behaviour, not just the button clicks.'],
                    ['Team development', 'Facilitated sessions on collaboration, communication and psychological safety — for teams that need to work differently, not know more.'],
                    ['Certification pathways', 'Independent exam preparation for the major schemes, with the exam booked directly with the scheme owner.'],
                    ['Group rates', 'Three or more people on the same course saves 15%, rising to 25% at ten. Applied automatically — no negotiation needed.'],
                ] as [$heading, $body])
                    <article class="card card-hover p-6">
                        <h3 class="text-base">{!! $heading !!}</h3>
                        <p class="mt-2 text-sm text-sand-600">{!! $body !!}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="proposal" class="scroll-mt-24 bg-brand-cream py-20">
        <div class="wrap grid gap-12 lg:grid-cols-[1fr_1.15fr]">
            <div>
                <p class="eyebrow">Request a proposal</p>
                <h2 class="mt-3">Tell us the gap. We will send a fixed price.</h2>
                <p class="lede mt-4">
                    Three short steps. A training advisor replies within two working hours, and you
                    get a written proposal within two working days.
                </p>

                <div class="card mt-8 p-6">
                    <h3 class="text-base">What happens next</h3>
                    <ol class="mt-4 space-y-4 text-sm">
                        @foreach ([
                            ['Within 2 working hours', 'A training advisor calls or emails to confirm the detail we need.'],
                            ['Within 2 working days', 'A written proposal: curriculum outline, delivery plan, fixed price.'],
                            ['Before you commit', 'We will tell you if an in-company group is more expensive than public seats. Usually it is not, from six people.'],
                        ] as $index => [$when, $what])
                            <li class="flex gap-3">
                                <span class="flex h-6 w-6 flex-none items-center justify-center rounded-full bg-orange-500 text-xs font-bold text-white">{{ $index + 1 }}</span>
                                <span><strong class="block">{{ $when }}</strong><span class="text-sand-600">{{ $what }}</span></span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <div class="card p-7">
                <livewire:public.corporate-inquiry-form />
            </div>
        </div>
    </section>

    @if ($faqs->isNotEmpty())
        <section class="py-20">
            <div class="wrap max-w-[820px]">
                <h2>Procurement questions</h2>
                <div class="mt-8 divide-y divide-sand-200 overflow-hidden rounded-card border border-sand-200">
                    @foreach ($faqs as $faq)
                        <details class="group">
                            <summary class="flex cursor-pointer items-center gap-3 p-5 font-semibold hover:bg-sand-50">
                                <span class="flex-1">{{ $faq->question }}</span>
                                <svg class="h-4 w-4 text-sand-400 transition group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 9 6 6 6-6"/></svg>
                            </summary>
                            <p class="border-t border-sand-100 bg-sand-50/50 p-5 text-sm text-sand-600">{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts.app>
