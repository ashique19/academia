<x-layouts.app title="Current Training Offers &amp; Group Discounts | Academia"
               description="Every discount Academia runs, on one page, with its end date and the published stacking ceiling.">

    <section class="bg-gradient-to-br from-orange-600 via-orange-500 to-gold-400 py-16 text-white">
        <div class="wrap grid gap-10 lg:grid-cols-[1.25fr_.75fr]">
            <div>
                <p class="eyebrow !text-white before:bg-white">Current offers</p>
                <h1 class="mt-3 text-white">Every discount we run, on one page, with its end date</h1>
                <p class="lede mt-4 text-white/90">
                    We would rather you found the offer here than hunted for a code at checkout.
                    Everything below is public, applies to everyone, and is calculated from the
                    published list price on each course page.
                </p>
            </div>

            <div class="rounded-card border border-white/25 bg-black/15 p-6">
                <h2 class="text-lg text-white">How our offers work</h2>
                <ul class="mt-4 space-y-2.5 text-sm text-white/90">
                    @foreach ([
                        'Every offer names its reason and its end date',
                        'List prices are never raised to create a discount',
                        'Combined savings stop at a published ' . $maxStack . '% ceiling',
                        'The basket shows which codes applied and which did not',
                    ] as $rule)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $rule }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    @if ($promotion)
        <section class="py-16">
            <div class="wrap">
                <p class="eyebrow">This month</p>
                <h2 class="mt-3">{{ $promotion->name }} — {{ $promotion->percentage }}% off</h2>
                <p class="lede mt-3 max-w-[70ch]">{{ $promotion->blurb }}</p>

                <div class="card mt-8 overflow-hidden">
                    <div class="grid gap-6 bg-gradient-to-r from-orange-600 to-gold-400 p-8 text-white md:grid-cols-[1fr_auto] md:items-center">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-white/80">Use code</p>
                            <p class="mt-1 font-display text-4xl font-semibold">{{ $promotion->code }}</p>
                            <p class="mt-2 max-w-[60ch] text-sm text-white/90">
                                Valid until {{ $promotion->ends_at->format('j F Y') }}.
                                Not combinable with Skills Credits rates.
                            </p>
                        </div>
                        <a href="{{ route('courses.index') }}" wire:navigate class="btn-gold btn-lg justify-self-start">
                            Browse discounted courses
                        </a>
                    </div>

                    {{-- Why the campaign exists. `reason` is a NOT NULL column
                         precisely so this can never be blank: an offer without
                         a stated reason is the beginning of a permanent sale. --}}
                    <div class="p-6">
                        <h3 class="text-sm font-semibold">Why we are running this</h3>
                        <p class="mt-2 text-sm text-sand-600">{{ $promotion->reason }}</p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="bg-brand-cream py-16">
        <div class="wrap">
            <p class="eyebrow">Corporate &amp; group</p>
            <h2 class="mt-3">Send more people, pay less per person</h2>
            <p class="lede mt-3 max-w-[70ch]">
                Applied automatically when you book multiple seats in one transaction — no
                negotiation, no quote needed, and it works on top of the monthly promotion up to
                the {{ $maxStack }}% ceiling.
            </p>

            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach ($groupTiers as $index => $tier)
                    <div @class(['card p-6', 'ring-2 ring-orange-500' => $index === 1])>
                        @if ($index === 1)
                            <span class="chip-orange">Most booked</span>
                        @else
                            <span class="chip">{{ $tier['min_seats'] }}+ people</span>
                        @endif
                        <p class="mt-4 font-display text-4xl font-semibold text-orange-600">
                            {{ $tier['percent'] }}% off
                        </p>
                        <p class="mt-2 text-sm text-sand-600">
                            {{ $tier['min_seats'] }} or more people on the same course and date.
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="wrap grid gap-6 md:grid-cols-2">
            <div class="rounded-card bg-green-600 p-8 text-white">
                <span class="chip bg-white/20 text-white">Early booking</span>
                <p class="mt-4 font-display text-4xl font-semibold">{{ $earlyBird['percent'] }}%</p>
                <h3 class="mt-2 text-white">Book more than {{ $earlyBird['days'] }} days ahead</h3>
                <p class="mt-2 text-sm text-white/85">
                    Planning early helps us confirm dates and fill rooms, so we pass some of that
                    back. Code <strong>{{ $earlyBird['code'] }}</strong>.
                </p>
                <a href="{{ route('schedule') }}" wire:navigate class="btn-gold mt-6">See the full schedule</a>
            </div>

            <div class="rounded-card bg-sand-900 p-8 text-white">
                <span class="chip bg-white/15 text-white">Annual commitment</span>
                <p class="mt-4 font-display text-4xl font-semibold">10–20%</p>
                <h3 class="mt-2 text-white">Academia Skills Credits</h3>
                <p class="mt-2 text-sm text-white/80">
                    Commit a training budget once and draw it down all year at a locked rate. This
                    replaces promotional codes rather than stacking with them — for most
                    organisations spending €10,000 or more a year it is the larger saving.
                </p>
                <a href="{{ route('corporate') }}#proposal" wire:navigate class="btn-gold mt-6">Talk to an advisor</a>
            </div>
        </div>

        <div class="wrap mt-10">
            <div class="card p-7">
                <h2 class="text-xl">The small print, in full</h2>
                <ul class="mt-5 space-y-3 text-sm text-sand-700">
                    @foreach ([
                        'All prices exclude VAT. Discounts apply to the course fee, not to exam fees charged by a certification scheme owner.',
                        'Codes are applied at checkout and shown as a separate line. The basket states the total saving and which code produced it.',
                        'Where several offers apply, they combine up to ' . $maxStack . '%. Above that ceiling the single highest-value combination applies.',
                        'Skills Credits rates are not combinable with promotional codes — the credit rate is already the discounted rate.',
                        'Promotions apply to new bookings made inside the stated window. We do not retro-apply them, and we do not withdraw them early.',
                        'Free cancellation up to 14 days before the start date applies to discounted bookings exactly as it does to full-price ones.',
                    ] as $clause)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $clause }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
