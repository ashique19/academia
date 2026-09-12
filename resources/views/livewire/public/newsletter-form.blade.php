<form wire:submit="submit" class="flex flex-col gap-3 sm:flex-row sm:items-end">
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <div class="flex-1">
        <label class="field-label" for="nl-email">Work email</label>
        <input id="nl-email" type="email" class="input" wire:model="email" required
               placeholder="you@company.com">
        @error('email') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-start gap-2 text-xs text-sand-600 sm:max-w-[16rem]">
        <input type="checkbox" wire:model="consent"
               class="mt-0.5 rounded border-sand-300 text-orange-500 focus:ring-orange-500">
        <span>Send me occasional training updates. Unsubscribe any time.</span>
    </label>

    <button type="submit" class="btn-primary shrink-0" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Subscribe</span>
        <span wire:loading wire:target="submit">…</span>
    </button>

    @error('consent') <p class="field-error w-full">{{ $message }}</p> @enderror
</form>
