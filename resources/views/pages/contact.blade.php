<x-layouts.app
    title="Contact Academia Training Solutions"
    description="Talk to a training advisor about public courses, in-company programmes, Skills Credits or invoicing. Course enquiries answered the same working day."
>
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[760px]">
            <p class="eyebrow !text-gold-400">Contact</p>
            <h1 class="mt-3 text-white">Talk to a training advisor</h1>
            <p class="lede mt-4 text-white/80">
                Not sure which course fits, or need something that is not in the catalogue?
                Tell us the situation and we will point you at the right option — including
                telling you when it is not us.
            </p>
        </div>
    </section>

    <section class="py-14">
        <div class="wrap grid gap-10 lg:grid-cols-[1.15fr_.85fr]">
            <div>
                <h2 class="text-xl">Send a message</h2>
                <p class="mt-2 text-sm text-sand-600">
                    A training advisor replies within one working day. For a fixed-price
                    corporate proposal, use the
                    <a href="{{ route('corporate') }}#proposal" class="font-semibold text-orange-600" wire:navigate>corporate enquiry form</a>
                    instead.
                </p>
                <div class="card mt-6 p-6">
                    <livewire:public.contact-form />
                </div>
            </div>

            <aside class="space-y-6">
                <div class="card p-6">
                    <h3 class="text-base">Direct channels</h3>
                    <ul class="mt-4 space-y-3 text-sm text-sand-700">
                        <li>
                            <a href="mailto:{{ config('academia.email') }}" class="font-semibold text-orange-600 hover:underline">
                                {{ config('academia.email') }}
                            </a>
                        </li>
                        <li>
                            <a href="tel:{{ preg_replace('/\s+/', '', config('academia.phone')) }}" class="font-semibold text-orange-600 hover:underline">
                                {{ config('academia.phone') }}
                            </a>
                            <span class="block text-xs text-sand-500">Monday–Friday 08:00–18:00 CET</span>
                        </li>
                    </ul>

                    <hr class="my-5 border-sand-200">

                    <h3 class="text-base">Response times</h3>
                    <ul class="mt-3 space-y-2 text-sm text-sand-700">
                        @foreach ([
                            'Course enquiries — same working day',
                            'Corporate proposals — 2 working days',
                            'Invoicing and admin — 1 working day',
                        ] as $promise)
                            <li class="flex gap-2">
                                <svg class="mt-0.5 h-4 w-4 flex-none text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                <span>{{ $promise }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-5 text-xs text-sand-500">
                        Courses are delivered at partner venues across Europe — see the
                        <a href="{{ route('locations') }}" class="underline" wire:navigate>locations page</a>
                        for each city.
                    </p>
                </div>

                <div class="card p-6">
                    <span class="chip-green">Usually within 2 hours</span>
                    <h3 class="mt-3 text-base">Rather talk it through?</h3>
                    <p class="mt-2 text-sm text-sand-600">
                        Pick a window and a training advisor calls you. No script, no pitch —
                        if we are not the right provider we will tell you who is.
                    </p>
                    <div class="mt-5">
                        <livewire:public.callback-form />
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layouts.app>
