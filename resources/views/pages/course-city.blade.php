<x-layouts.app :title="$course->display_title . ' in ' . $city->name . ' | Academia'"
               :description="$course->summary">
    <section class="bg-sand-900 py-14 text-white">
        <div class="wrap">
            <nav aria-label="Breadcrumb" class="text-xs text-white/60">
                <a href="{{ route('courses.show', $course) }}" wire:navigate class="hover:text-white">{{ $course->display_title }}</a>
                <span class="mx-1">›</span><span>{{ $city->name }}</span>
            </nav>
            <h1 class="mt-3 text-white">{{ $course->display_title }} in {{ $city->name }}</h1>
            <p class="lede mt-3 max-w-[65ch] text-white/75">
                {{ $sessions->count() }} confirmed {{ \Illuminate\Support\Str::plural('date', $sessions->count()) }}
                in {{ $city->name }}, maximum {{ $course->max_participants }} participants.
            </p>
        </div>
    </section>

    <div class="wrap py-12">
        <div class="grid gap-3">
            @foreach ($sessions as $session)
                <x-session-row :session="$session" />
            @endforeach
        </div>

        <a href="{{ route('courses.show', $course) }}" wire:navigate class="btn-primary btn-lg mt-8">
            Full course details and booking
        </a>
    </div>
    <x-cta-band />
</x-layouts.app>
