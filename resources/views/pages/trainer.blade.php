<x-layouts.app :title="$trainer->name . ' | Academia Training Solutions'">
    <div class="wrap max-w-[760px] py-14">
        <h1>{{ $trainer->name }}</h1>
        <p class="lede mt-2">{{ $trainer->headline }}</p>
        @if ($trainer->bio_full)
            <p class="mt-6 leading-relaxed text-sand-700">{{ $trainer->bio_full }}</p>
        @endif
        @if (filled($trainer->certifications))
            <h2 class="mt-10">Certifications</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($trainer->certifications as $certification)
                    <span class="chip">{{ $certification }}</span>
                @endforeach
            </div>
        @endif
    </div>
    <x-cta-band />
</x-layouts.app>
