<form wire:submit="submit" data-validate class="mt-4 space-y-3">
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <p data-form-error hidden class="field-error !mt-0" role="alert">
        Please enter your work email.
    </p>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
        <div class="min-w-0 flex-1">
            <label class="sr-only" for="ac-outline-email">Work email</label>
            <input id="ac-outline-email" type="email" required autocomplete="email"
                   class="input" wire:model="email" placeholder="Your work email">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary shrink-0" wire:loading.attr="disabled" wire:target="submit">
            <span wire:loading.remove wire:target="submit">Send me the outline</span>
            <span wire:loading wire:target="submit">Sending…</span>
        </button>
    </div>
</form>
