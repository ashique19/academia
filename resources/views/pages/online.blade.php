<x-layouts.app title="Live Online Professional Training | Academia"
               description="Instructor-led virtual classrooms, maximum 12 participants. Join from anywhere in Europe with no travel cost.">
    <section class="bg-sand-900 py-16 text-white">
        <div class="wrap max-w-[760px]">
            <p class="eyebrow !text-gold-400">Live online training</p>
            <h1 class="mt-3 text-white">Learn from anywhere. Nothing watered down.</h1>
            <p class="lede mt-4 text-white/80">
                Instructor-led virtual classrooms with a maximum of 12 participants, breakout rooms,
                and the same expert, materials and certificate as the classroom version.
            </p>
        </div>
    </section>

    <div class="wrap py-14">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['Maximum 12 participants', 'Small enough that everyone is asked a direct question and gets feedback on their own work.'],
                ['Sessions recorded', 'Available for 90 days where the trainer and participants consent — recording a session in which people discuss their own workplace has GDPR implications.'],
                ['Your own time zone', 'Every start time is shown in CET and converted to your local time automatically.'],
                ['Same certificate', 'Identical to the classroom version. The delivery mode changes; the standard does not.'],
            ] as [$heading, $body])
                <div class="card p-5">
                    <h2 class="text-base">{{ $heading }}</h2>
                    <p class="mt-2 text-sm text-sand-600">{{ $body }}</p>
                </div>
            @endforeach
        </div>

        @if ($courses->isNotEmpty())
            <h2 class="mt-14">Popular online courses</h2>
            <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>
        @endif
    </div>
    <x-cta-band />
</x-layouts.app>
