<form wire:submit="submit" class="space-y-4">

    {{-- Honeypot. Hidden from humans and from assistive technology; bots fill
         it. Paired with a timing check in SpamGuard, and BOTH fail silently. --}}
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    @if ($this->schedules->isNotEmpty())
        <fieldset>
            <legend class="field-label">Choose a date</legend>
            <div class="max-h-64 space-y-2 overflow-y-auto pr-1">
                @foreach ($this->schedules as $session)
                    <label wire:key="session-{{ $session->id }}"
                           @class([
                               'flex cursor-pointer items-center gap-3 rounded-lg border p-3 text-sm transition',
                               'border-orange-500 bg-orange-50' => $scheduleId === $session->id,
                               'border-sand-200 hover:bg-sand-50' => $scheduleId !== $session->id,
                               'opacity-60' => ! $session->isBookable(),
                           ])>
                        <input type="radio" wire:model.live="scheduleId" value="{{ $session->id }}"
                               @disabled(! $session->isBookable())
                               class="text-orange-500 focus:ring-orange-500">

                        <span class="flex-1">
                            <span class="font-semibold">{{ $session->starts_at->format('j M Y') }}</span>
                            <span class="block text-xs text-sand-500">
                                {{ $session->location_label }} · {{ $session->deliveryMode?->name }}
                            </span>
                        </span>

                        @if ($session->seats_available <= 0)
                            <span class="chip-gold">Full</span>
                        @elseif ($session->is_nearly_full)
                            <span class="chip-gold">{{ $session->seats_available }} left</span>
                        @else
                            <span class="chip-green">Available</span>
                        @endif
                    </label>
                @endforeach
            </div>
            @error('scheduleId') <p class="field-error">{{ $message }}</p> @enderror
        </fieldset>
    @else
        <p class="rounded-lg bg-sand-50 p-3 text-sm text-sand-600">
            No public dates are scheduled yet. Register your interest and we will tell you as
            soon as one is confirmed.
        </p>
    @endif

    <div>
        <label class="field-label" for="reg-name">Your name</label>
        <input id="reg-name" type="text" class="input" wire:model="name" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="reg-email">Work email</label>
        <input id="reg-email" type="email" class="input" wire:model="email" required>
        @error('email') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-3 sm:grid-cols-2">
        <div>
            <label class="field-label" for="reg-phone">Phone</label>
            <input id="reg-phone" type="tel" class="input" wire:model="phone">
        </div>
        <div>
            <label class="field-label" for="reg-company">Company</label>
            <input id="reg-company" type="text" class="input" wire:model="company">
        </div>
    </div>

    <label class="flex items-start gap-2.5 text-xs text-sand-600">
        {{-- Unticked by default. A pre-ticked box is not consent under GDPR
             Art. 7, and the burden of proof sits with us. --}}
        <input type="checkbox" wire:model="consent"
               class="mt-0.5 rounded border-sand-300 text-orange-500 focus:ring-orange-500">
        <span>
            I am happy for Academia to contact me about this enquiry. We keep enquiry data for
            24 months and never share it.
            <a href="{{ route('legal', 'privacy') }}" class="underline" wire:navigate>Privacy notice</a>.
        </span>
    </label>
    @error('consent') <p class="field-error">{{ $message }}</p> @enderror

    <button type="submit" class="btn-primary w-full">
        <span wire:loading.remove wire:target="submit">Register interest</span>
        <span wire:loading wire:target="submit">Sending…</span>
    </button>

    <a href="{{ route('corporate') }}#proposal" wire:navigate class="btn-ghost w-full">
        Train my team instead
    </a>

    <ul class="space-y-1.5 border-t border-sand-200 pt-4 text-xs text-sand-500">
        <li>Free cancellation up to 14 days before</li>
        <li>Send a colleague if you cannot attend</li>
        <li>If we cancel: full refund and we cover your non-refundable travel</li>
    </ul>
</form>
