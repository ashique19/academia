<section class="bg-gradient-to-r from-orange-600 to-orange-500 py-16 text-white">
    <div class="wrap flex flex-wrap items-center justify-between gap-6">
        <div>
            <h2 class="text-white">Not sure which course fits?</h2>
            <p class="mt-2 max-w-[52ch] text-white/85">
                Tell us the capability gap and the number of people. A training advisor replies
                within two working hours, and if we are not the right provider we will say so.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('corporate') }}#proposal" class="btn-gold btn-lg" wire:navigate>Talk to an advisor</a>
            <a href="{{ route('courses.index') }}" class="btn-light btn-lg" wire:navigate>Browse courses</a>
        </div>
    </div>
</section>
