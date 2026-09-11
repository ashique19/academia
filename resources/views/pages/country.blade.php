<x-layouts.app :title="'Training Courses in ' . $country->name . ' | Academia'">
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('home') }}" wire:navigate class="hover:text-white">Home</a>
                <span class="mx-1">›</span>
                <a href="{{ route('locations') }}" wire:navigate class="hover:text-white">Classroom training</a>
                <span class="mx-1">›</span><span>{{ $country->name }}</span>
            </nav>
            <h1 class="mt-3 text-white">Training in {{ $country->name }}</h1>
        </div>
    </section>

    <div class="wrap py-14">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($cities as $city)
                <a href="{{ route('locations.city', [$country, $city]) }}" wire:navigate class="card card-hover p-5">
                    <h2 class="text-lg">{{ $city->name }}</h2>
                    <p class="mt-1.5 text-sm text-sand-600">
                        {{ $city->upcoming_sessions_count }} upcoming
                        {{ \Illuminate\Support\Str::plural('date', $city->upcoming_sessions_count) }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
    <x-cta-band />
</x-layouts.app>
