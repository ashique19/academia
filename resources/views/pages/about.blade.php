<x-layouts.app title="About Academia Training Solutions">
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[760px]">
            <h1 class="text-white">A European training academy built by practitioners</h1>
            <p class="lede mt-4 text-white/80">
                Academia Training Solutions is a trade name of {{ config('academia.legal_entity') }}.
                We deliver practical professional training online, at client premises, and in public
                classrooms across Europe.
            </p>
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
