<x-layouts.app :title="'Professional Training Courses in ' . $city->name . ' | Academia'"
               :description="'Scheduled classroom training in ' . $city->name . '. ' . $sessions->count() . ' upcoming dates across business, finance, technology and compliance.'">

    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-white">Home</a>
                <span class="mx-1">›</span>
                <a href="{{ route('locations') }}" wire:navigate class="hover:text-white">Classroom training</a>
                <span class="mx-1">›</span>
                <a href="{{ route('locations.country', $city->country) }}" wire:navigate class="hover:text-white">{{ $city->country->name }}</a>
                <span class="mx-1">›</span><span>{{ $city->name }}</span>
            </nav>

            <h1 class="mt-3 text-white">Professional training in {{ $city->name }}</h1>
            <p class="lede mt-3 max-w-[65ch] text-white/75">
                {{ $sessions->count() }} upcoming {{ \Illuminate\Support\Str::plural('date', $sessions->count()) }}
                · {{ $courses->count() }} {{ \Illuminate\Support\Str::plural('course', $courses->count()) }}
                · maximum 14 participants
            </p>
        </div>
    </section>

    <div class="wrap grid gap-10 py-12 lg:grid-cols-[1fr_340px]">
        <div>
            @if ($city->intro)
                <div class="prose-academia">
                    <p class="lede">{{ $city->intro }}</p>
                </div>
            @else
                {{-- 26 pages differing only by a city name is spun content and
                     will not rank. The admin flags this; the page does not
                     fabricate copy to fill the gap. --}}
                @if (config('app.debug'))
                    <div class="card border-dashed border-gold-300 bg-gold-50 p-5 text-sm">
                        <strong>Content needed.</strong> This city has no hand-written introduction.
                        Add 120–200 words of genuinely specific detail (which industries cluster here,
                        why this venue, transport from the airport) before launch — templated city
                        copy will not rank.
                    </div>
                @endif
            @endif

            <h2 class="mt-10">Upcoming dates in {{ $city->name }}</h2>
            <div class="mt-5 grid gap-3">
                @foreach ($sessions as $session)
                    <x-session-row :session="$session" />
                @endforeach
            </div>

            @if ($courses->isNotEmpty())
                <h2 class="mt-12">Courses running in {{ $city->name }}</h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    @foreach ($courses->take(8) as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>
            @endif

            @if ($nearby->isNotEmpty())
                <h2 class="mt-12">Nearby cities</h2>
                <div class="mt-5 flex flex-wrap gap-2">
                    @foreach ($nearby as $nearbyCity)
                        <a href="{{ route('locations.city', [$nearbyCity->country, $nearbyCity]) }}" wire:navigate
                           class="chip hover:bg-sand-200">
                            {{ $nearbyCity->name }} · {{ (int) $nearbyCity->distance_km }} km
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
            @foreach ($city->venues as $venue)
                <div class="card p-5">
                    <h3 class="text-base">{{ $venue->name }}</h3>
                    @if ($venue->address_line1)
                        <p class="mt-1.5 text-sm text-sand-600">{{ $venue->full_address }}</p>
                    @endif
                    @if ($venue->transport_notes)
                        <p class="mt-3 text-sm text-sand-600">{{ $venue->transport_notes }}</p>
                    @endif
                </div>
            @endforeach

            <div class="card border-green-200 bg-green-50 p-5">
                <h3 class="text-base">In-company delivery in {{ $city->name }}</h3>
                <p class="mt-2 text-sm text-sand-600">
                    From six participants, delivering at your own premises is usually cheaper than
                    individual seats — and far easier to schedule.
                </p>
                <a href="{{ route('corporate') }}#proposal" wire:navigate class="btn-green mt-4 w-full">
                    Request a proposal
                </a>
            </div>
        </aside>
    </div>

    <x-cta-band />
</x-layouts.app>
