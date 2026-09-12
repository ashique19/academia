<form wire:submit="submit" class="space-y-4">
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="field-label" for="cb-name">Name</label>
            <input id="cb-name" type="text" class="input" wire:model="name" required>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="cb-phone">Phone</label>
            <input id="cb-phone" type="tel" class="input" wire:model="phone" required>
            @error('phone') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="field-label" for="cb-email">Work email</label>
            <input id="cb-email" type="email" class="input" wire:model="email" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="cb-when">Best time</label>
            <select id="cb-when" class="select" wire:model="preferredWindow">
                <option>As soon as possible</option>
                <option>Today, morning</option>
                <option>Today, afternoon</option>
                <option>Tomorrow, morning</option>
                <option>Tomorrow, afternoon</option>
                <option>Later this week</option>
            </select>
            @error('preferredWindow') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <label class="flex items-start gap-2.5 text-xs text-sand-600">
        <input type="checkbox" wire:model="consent"
               class="mt-0.5 rounded border-sand-300 text-orange-500 focus:ring-orange-500">
        <span>
            I am happy for Academia to call me about training. We keep enquiry data for 24 months.
            <a href="{{ route('legal', 'privacy') }}" class="underline" wire:navigate>Privacy notice</a>.
        </span>
    </label>
    @error('consent') <p class="field-error">{{ $message }}</p> @enderror

    <button type="submit" class="btn-primary w-full" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Request a callback</span>
        <span wire:loading wire:target="submit">Sending…</span>
    </button>
</form>
