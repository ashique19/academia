<form wire:submit="submit" class="flex flex-col gap-3">
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    {{-- Email + Subscribe share one baseline. Consent is its own row so wrapped
         legal copy cannot shove the button out of vertical alignment. --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="min-w-0 flex-1">
            <label class="mb-1.5 block text-sm font-semibold text-sand-200" for="nl-email">Work email</label>
            <input id="nl-email" type="email" class="input !h-[42px] !py-0" wire:model="email" required
                   placeholder="you@company.com" autocomplete="email">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="btn-primary !h-[42px] shrink-0 px-6"
                wire:loading.attr="disabled"
                wire:target="submit">
            Subscribe
        </button>
    </div>

    <label class="grid grid-cols-[1rem_1fr] items-start gap-x-2.5 text-xs leading-5 text-sand-300">
        <input type="checkbox" wire:model="consent"
               class="mt-[3px] size-4 shrink-0 justify-self-center rounded border-sand-500 bg-sand-800 text-orange-500 focus:ring-orange-500 focus:ring-offset-0">
        <span>Send me occasional training updates. Unsubscribe any time.</span>
    </label>

    @error('consent') <p class="field-error">{{ $message }}</p> @enderror
</form>
