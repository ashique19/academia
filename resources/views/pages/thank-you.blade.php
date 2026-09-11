@php
    // A distinct URL per conversion type, so analytics can fire a goal on the
    // page view rather than on a click that may never have succeeded.
    $copy = match ($type) {
        'corporate'    => ['Proposal request received', 'A training advisor will be in touch within two working hours, and you will have a written proposal within two working days.'],
        'registration' => ['You are registered', 'We have emailed your confirmation and joining details. Your place is held.'],
        'interest'     => ['Interest registered', 'We will tell you as soon as a date is confirmed for this course.'],
        'brochure'     => ['Outline on its way', 'Check your inbox — the two-page course outline is there now.'],
        'callback'     => ['Callback booked', 'A training advisor will call you in the window you chose.'],
        'newsletter'   => ['Almost there', 'Click the link in the confirmation email to complete your subscription.'],
        default        => ['Thank you', 'We have received your message.'],
    };
@endphp

<x-layouts.app :title="$copy[0] . ' | Academia'" robots="noindex,follow">
    <section class="py-24">
        <div class="wrap max-w-[620px] text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                <svg class="h-8 w-8 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <h1 class="mt-6">{{ $copy[0] }}</h1>
            <p class="lede mt-4">{{ $copy[1] }}</p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('courses.index') }}" wire:navigate class="btn-primary">Browse courses</a>
                <a href="{{ route('schedule') }}" wire:navigate class="btn-ghost">See upcoming dates</a>
            </div>
        </div>
    </section>
</x-layouts.app>
