<x-layouts.app title="Our Faculty | Academia Training Solutions">
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap"><h1 class="text-white">Our faculty</h1></div>
    </section>
    <div class="wrap grid gap-5 py-14 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($trainers as $trainer)
            <a href="{{ route('trainers.show', $trainer) }}" wire:navigate class="card card-hover p-6">
                <h2 class="text-lg">{{ $trainer->name }}</h2>
                <p class="mt-1 text-sm text-sand-600">{{ $trainer->headline }}</p>
                @if ($trainer->years_experience)
                    <p class="mt-3 text-xs text-sand-500">{{ $trainer->years_experience }} years in the field</p>
                @endif
            </a>
        @endforeach
    </div>
</x-layouts.app>
