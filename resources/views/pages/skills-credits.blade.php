<x-layouts.app
    title="Academia Skills Credits — Prepaid Training Budget"
    description="Commit training budget once, draw it down all year at a locked rate. Team, Business and Enterprise tiers with 10–20% off list price."
>
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[820px]">
            <p class="eyebrow !text-gold-400">Academia Skills Credits</p>
            <h1 class="mt-3 text-white">Commit the budget once. Spend it all year, on anything.</h1>
            <p class="lede mt-4 text-white/80">
                Buy training credit up front and draw it down against any course, any delivery mode,
                any city, for twelve months. Your rate is locked at purchase, seats pool across the
                whole organisation, and you get one invoice instead of forty.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="wrap">
            <div class="grid gap-5 lg:grid-cols-3">
                @foreach ([
                    [
                        'name' => 'Team',
                        'commit' => '€10,000',
                        'discount' => '10%',
                        'worth' => '≈ €11,100 of training at list price',
                        'for' => 'One department, a handful of people per quarter',
                        'featured' => false,
                    ],
                    [
                        'name' => 'Business',
                        'commit' => '€25,000',
                        'discount' => '15%',
                        'worth' => '≈ €29,400 of training at list price',
                        'for' => 'Several teams, a planned annual training calendar',
                        'featured' => true,
                    ],
                    [
                        'name' => 'Enterprise',
                        'commit' => '€60,000',
                        'discount' => '20%',
                        'worth' => '≈ €75,000 of training at list price',
                        'for' => 'Multi-country programmes with central L&D ownership',
                        'featured' => false,
                    ],
                ] as $tier)
                    <article @class(['card p-7', 'ring-2 ring-orange-500' => $tier['featured']])>
                        @if ($tier['featured'])
                            <span class="chip-orange">Most chosen</span>
                        @else
                            <span class="chip">{{ $tier['name'] }}</span>
                        @endif
                        <h2 class="mt-4 text-xl">{{ $tier['name'] }}</h2>
                        <p class="mt-3 font-display text-3xl font-semibold text-sand-900">
                            {{ $tier['commit'] }}
                            <span class="block text-sm font-sans font-normal text-sand-500">committed, excl. VAT</span>
                        </p>
                        <p class="mt-4 font-display text-2xl text-orange-600">{{ $tier['discount'] }} off</p>
                        <p class="mt-2 text-sm text-sand-600">{{ $tier['worth'] }}</p>
                        <p class="mt-4 text-xs text-sand-500"><strong>Right for:</strong> {{ $tier['for'] }}</p>
                        <a href="{{ route('corporate') }}#proposal" wire:navigate
                           @class(['btn mt-6 w-full justify-center', 'btn-primary' => $tier['featured'], 'btn-ghost' => ! $tier['featured']])>
                            Discuss this tier
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Valid 12 months', 'Book whenever the need appears — no expiry pressure at quarter end.'],
                    ['Pooled across teams', 'Any employee, any department, any course in the catalogue.'],
                    ['Live budget view', 'See what is committed, spent and remaining, by team, at any time.'],
                    ['One invoice', 'A single PO and a single invoice instead of one per booking.'],
                ] as [$heading, $body])
                    <div class="card p-5">
                        <h3 class="text-base">{{ $heading }}</h3>
                        <p class="mt-2 text-sm text-sand-600">{{ $body }}</p>
                    </div>
                @endforeach
            </div>

            <p class="mt-8 max-w-[70ch] text-xs text-sand-500">
                Credits are drawn at the list price less your tier discount. Unused credit at twelve
                months can be rolled into a renewal. This is a volume commitment, not a discount
                code — the rate does not move for anyone who has not committed budget. Skills Credits
                rates are not combinable with promotional codes.
            </p>
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
