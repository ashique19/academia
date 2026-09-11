@props(['session'])

<div class="flex flex-wrap items-center gap-4 rounded-card border border-sand-200 p-4">

    <div class="flex h-14 w-14 flex-none flex-col items-center justify-center rounded-lg bg-sand-100">
        <span class="font-display text-lg font-semibold leading-none">{{ $session->starts_at->format('j') }}</span>
        <span class="text-[10px] font-bold uppercase text-sand-500">{{ $session->starts_at->format('M') }}</span>
    </div>

    <div class="min-w-0 flex-1">
        <a href="{{ route('courses.show', $session->course) }}" wire:navigate
           class="block truncate font-semibold hover:text-orange-600">
            {{ $session->course->display_title }}
        </a>
        <p class="mt-0.5 text-xs text-sand-500">
            {{ $session->location_label }} · {{ $session->deliveryMode?->name }} ·
            {{-- data-utc/data-zone are read by app.js, which appends the
                 viewer's own local time when their zone differs. --}}
            <span data-utc="{{ $session->starts_at->toIso8601String() }}" data-zone="{{ $session->timezone }}">
                {{ $session->starts_at->setTimezone($session->timezone)->format('H:i') }} {{ $session->starts_at->setTimezone($session->timezone)->format('T') }}
            </span>
        </p>
    </div>

    <div class="flex items-center gap-3">
        @if ($session->seats_available <= 0)
            <span class="chip-gold">Full — join waitlist</span>
        @elseif ($session->is_nearly_full)
            {{-- Honest below 3 seats; above it, a countdown is a dark pattern. --}}
            <span class="chip-gold">{{ $session->seats_available }} seats left</span>
        @else
            <span class="chip-green">Seats available</span>
        @endif

        <a href="{{ route('courses.show', $session->course) }}#book" wire:navigate class="btn-ghost">Details</a>
    </div>
</div>
