<x-layouts.app title="Classroom Training Across Europe | Academia"
               description="Public classroom courses in European cities, with real scheduled dates and a maximum of 14 participants.">

    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <h1 class="text-white">Classroom training across Europe</h1>
            <p class="lede mt-3 max-w-[65ch] text-white/75">
                Central, well-connected venues with real scheduled dates. Maximum 14 participants,
                published and contractual.
            </p>
        </div>
    </section>

    <div class="wrap py-14">
        {{-- Only countries with a city that has a genuinely scheduled session
             appear here — the same gate that makes each city page 404 when it
             has nothing real to show. --}}
        @foreach ($countries as $country)
            <section class="mb-12">
                <h2 class="text-2xl">
                    <a href="{{ route('locations.country', $country) }}" wire:navigate class="hover:text-orange-600">
                        {{ $country->name }}
                    </a>
                </h2>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($country->cities as $city)
                        <a href="{{ route('locations.city', [$country, $city]) }}" wire:navigate class="card card-hover p-5">
                            <h3 class="text-lg">{{ $city->name }}</h3>
                            <p class="mt-1.5 text-sm text-sand-600">
                                {{ $city->upcoming_sessions_count }} upcoming
                                {{ \Illuminate\Support\Str::plural('date', $city->upcoming_sessions_count) }}
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

    <x-cta-band />
</x-layouts.app>
