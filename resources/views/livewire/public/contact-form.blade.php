<form wire:submit="submit" class="space-y-4">
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="field-label" for="contact-name">Your name</label>
            <input id="contact-name" type="text" class="input" wire:model="name" required>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="contact-email">Work email</label>
            <input id="contact-email" type="email" class="input" wire:model="email" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="field-label" for="contact-phone">Phone <span class="font-normal text-sand-500">(optional)</span></label>
            <input id="contact-phone" type="tel" class="input" wire:model="phone">
            @error('phone') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="field-label" for="contact-company">Company <span class="font-normal text-sand-500">(optional)</span></label>
            <input id="contact-company" type="text" class="input" wire:model="company">
            @error('company') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="field-label" for="contact-subject">Subject</label>
        <input id="contact-subject" type="text" class="input" wire:model="subject" required
               placeholder="e.g. Course recommendation, invoice question, partnership">
        @error('subject') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="field-label" for="contact-message">How can we help?</label>
        <textarea id="contact-message" class="textarea" rows="5" wire:model="message" required></textarea>
        @error('message') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <label class="flex items-start gap-2.5 text-xs text-sand-600">
        <input type="checkbox" wire:model="consent"
               class="mt-0.5 rounded border-sand-300 text-orange-500 focus:ring-orange-500">
        <span>
            I am happy for Academia to contact me about this enquiry. We keep enquiry data for
            24 months and never sell it.
            <a href="{{ route('legal', 'privacy') }}" class="underline" wire:navigate>Privacy notice</a>.
        </span>
    </label>
    @error('consent') <p class="field-error">{{ $message }}</p> @enderror

    <button type="submit" class="btn-primary w-full sm:w-auto" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="submit">Send message</span>
        <span wire:loading wire:target="submit">Sending…</span>
    </button>
</form>
