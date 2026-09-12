@php
    $ceiling = (int) config('academia.promotions.max_stack_percent', 30);
    $rows = [
        ['Group size', 'Maximum 14 in a classroom, 12 online — published and contractual', 'Rarely published'],
        ['Who teaches', '10+ years in the field, still practising, reference-checked and audition-tested', '“Experienced trainers”'],
        ['List price', 'One published list price per course, visible before you enquire — the number every discount is calculated from', 'A list price that appears only as the crossed-out number beside the offer'],
        ['Discounts', "Published, time-boxed, and stackable to a stated {$ceiling}% ceiling — the same offer for everyone, no negotiation", 'A permanent “40–60% off” running against a price nobody pays'],
        ['Dates', 'Real dates with real seat counts, shown before you enquire', 'Dates and prices behind an enquiry form'],
        ['If we cancel', 'Full refund or free transfer, and we reimburse non-refundable travel', 'Right to reschedule or substitute a virtual course'],
        ['Your data', 'Processed and stored in the EU under a signed DPA', 'Often outside the EU'],
    ];
@endphp

<x-layouts.app
    title="Why Our Price Is What It Is | Academia"
    description="We discount openly — and we show you what the discount is from. A like-for-like comparison against high-volume providers."
>
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[820px]">
            <p class="eyebrow !text-gold-400">Straight answer</p>
            <h1 class="mt-3 text-white">We discount openly — and we show you what the discount is from</h1>
            <p class="lede mt-4 text-white/80">
                Everyone in this market runs offers. The difference is whether the crossed-out
                number was ever a real price. Ours is: it is published, it is what you pay outside
                a promotion window, and every reduction has a named reason and an end date.
            </p>
        </div>
    </section>

    <section class="py-16">
        <div class="wrap">
            <div class="card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-sand-50 text-xs uppercase tracking-wider text-sand-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold"></th>
                                <th class="px-5 py-3 font-semibold text-sand-800">Academia</th>
                                <th class="px-5 py-3 font-semibold">Typical high-volume provider</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sand-100">
                            @foreach ($rows as [$label, $ours, $theirs])
                                <tr>
                                    <th class="px-5 py-4 align-top font-semibold text-sand-800">{{ $label }}</th>
                                    <td class="px-5 py-4 align-top text-sand-700">
                                        <span class="mr-1.5 inline text-green-600">✓</span>{{ $ours }}
                                    </td>
                                    <td class="px-5 py-4 align-top text-sand-500">{{ $theirs }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mt-8 p-7">
                <h2 class="text-lg">Our three promotion rules, in writing</h2>
                <ul class="mt-4 space-y-3 text-sm text-sand-700">
                    @foreach ([
                        'Every offer names its reason and its end date. No permanent sale, no countdown that resets when you reload.',
                        'List prices are never inflated to create a discount. The price outside a promotion window is the price we charged before it and after it.',
                        "Stacking stops at {$ceiling}%. Combine a monthly promotion, a group rate and early booking and the total saving is capped — published, so you can check it.",
                    ] as $rule)
                        <li class="flex gap-3">
                            <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            <span>{{ $rule }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('offers') }}" class="btn-primary" wire:navigate>See what is running now</a>
                    <a href="{{ route('skills-credits') }}" class="btn-ghost" wire:navigate>Skills Credits tiers</a>
                </div>
            </div>
        </div>
    </section>

    <x-cta-band />
</x-layouts.app>
