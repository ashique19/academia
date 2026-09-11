<form wire:submit="submit" class="space-y-5">

    {{-- Honeypot + timing. Both fail silently — a bot told it was detected
         adapts; a bot that believes it succeeded does not. --}}
    <div class="hidden" aria-hidden="true">
        <label>Website<input type="text" wire:model="website" tabindex="-1" autocomplete="off"></label>
    </div>

    <ol class="flex items-center gap-2 text-xs font-semibold" aria-label="Progress">
        @foreach (['The need', 'Your company', 'Contact'] as $index => $label)
            @php $stepNumber = $index + 1; @endphp
            <li class="flex flex-1 items-center gap-2">
                <span @class([
                    'flex h-6 w-6 flex-none items-center justify-center rounded-full text-[11px]',
                    'bg-orange-500 text-white'  => $step >= $stepNumber,
                    'bg-sand-200 text-sand-500' => $step < $stepNumber,
                ])>{{ $stepNumber }}</span>
                <span @class(['hidden sm:inline', 'text-sand-400' => $step < $stepNumber])>{{ $label }}</span>
                @if ($stepNumber < 3)
                    <span @class(['h-px flex-1', 'bg-orange-500' => $step > $stepNumber, 'bg-sand-200' => $step <= $stepNumber])></span>
                @endif
            </li>
        @endforeach
    </ol>

    {{-- STEP 1 — the need.
         Deliberately first: participants and delivery mode decide whether a
         lead is worth a call, and they are the least likely to be abandoned.
         Never open a B2B form with "Budget". --}}
    @if ($step === 1)
        <div class="space-y-4">
            <div>
                <label class="field-label" for="ci-topic">What training do you need?</label>
                <input id="ci-topic" type="text" class="input" wire:model="topic"
                       placeholder="e.g. Power BI for the finance team, or first-time manager development">
                @error('topic') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="ci-mode">Preferred delivery</label>
                    <select id="ci-mode" class="select" wire:model="deliveryModeId">
                        <option value="">Choose…</option>
                        @foreach ($this->deliveryModes as $deliveryMode)
                            <option value="{{ $deliveryMode->id }}">{{ $deliveryMode->name }}</option>
                        @endforeach
                    </select>
                    @error('deliveryModeId') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="field-label" for="ci-participants">How many people?</label>
                    <input id="ci-participants" type="number" min="1" class="input" wire:model="participants"
                           placeholder="An estimate is fine">
                    @error('participants') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="field-label" for="ci-date">Preferred start (optional)</label>
                <input id="ci-date" type="date" class="input" wire:model="preferredStartDate">
                @error('preferredStartDate') <p class="field-error">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif

    {{-- STEP 2 — the company --}}
    @if ($step === 2)
        <div class="space-y-4">
            <div>
                <label class="field-label" for="ci-company">Company name</label>
                <input id="ci-company" type="text" class="input" wire:model="companyName">
                @error('companyName') <p class="field-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="ci-country">Country</label>
                    <select id="ci-country" class="select" wire:model.live="countryId">
                        <option value="">Choose…</option>
                        @foreach ($this->countries as $countryOption)
                            <option value="{{ $countryOption->id }}">{{ $countryOption->name }}</option>
                        @endforeach
                    </select>
                    @error('countryId') <p class="field-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="field-label" for="ci-city">City (optional)</label>
                    <select id="ci-city" class="select" wire:model="cityId" @disabled($this->citiesInCountry->isEmpty())>
                        <option value="">Choose…</option>
                        @foreach ($this->citiesInCountry as $cityOption)
                            <option value="{{ $cityOption->id }}">{{ $cityOption->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="ci-sector">Sector (optional)</label>
                    <input id="ci-sector" type="text" class="input" wire:model="sector">
                </div>
                <div>
                    <label class="field-label" for="ci-size">Company size (optional)</label>
                    <select id="ci-size" class="select" wire:model="companySize">
                        <option value="">Choose…</option>
                        @foreach (['1–50', '51–250', '251–1,000', '1,001–5,000', '5,000+'] as $size)
                            <option value="{{ $size }}">{{ $size }} employees</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 3 — contact --}}
    @if ($step === 3)
        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="ci-name">Your name</label>
                    <input id="ci-name" type="text" class="input" wire:model="contactName">
                    @error('contactName') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="field-label" for="ci-title">Job title (optional)</label>
                    <input id="ci-title" type="text" class="input" wire:model="jobTitle">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="ci-email">Work email</label>
                    <input id="ci-email" type="email" class="input" wire:model.blur="email">
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                    {{-- Warns, never blocks. Plenty of legitimate small-company
                         buyers use Gmail, and blocking them loses real revenue. --}}
                    @if ($this->emailWarning)
                        <p class="mt-1.5 text-xs text-sand-500">{{ $this->emailWarning }}</p>
                    @endif
                </div>
                <div>
                    <label class="field-label" for="ci-phone">Phone (optional)</label>
                    <input id="ci-phone" type="tel" class="input" wire:model="phone">
                </div>
            </div>

            <div>
                <label class="field-label" for="ci-budget">Budget range (optional)</label>
                <select id="ci-budget" class="select" wire:model="budgetRange">
                    <option value="">Choose…</option>
                    @foreach (['Under €5,000', '€5,000–15,000', '€15,000–50,000', 'Over €50,000', 'Not yet defined'] as $range)
                        <option value="{{ $range }}">{{ $range }}</option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-xs text-sand-500">
                    A range helps us scope realistically. “Not yet defined” is a perfectly good answer.
                </p>
            </div>

            <div>
                <label class="field-label" for="ci-message">Anything else? (optional)</label>
                <textarea id="ci-message" rows="4" class="textarea" wire:model="message"></textarea>
            </div>

            <label class="flex items-start gap-2.5 text-xs text-sand-600">
                <input type="checkbox" wire:model="consent"
                       class="mt-0.5 rounded border-sand-300 text-orange-500 focus:ring-orange-500">
                <span>
                    I am happy for Academia to contact me about this enquiry. Enquiry data is kept for
                    24 months, processed inside the EEA, and never shared.
                    <a href="{{ route('legal', 'privacy') }}" class="underline" wire:navigate>Privacy notice</a>.
                </span>
            </label>
            @error('consent') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    @endif

    <div class="flex items-center gap-3 pt-2">
        @if ($step > 1)
            <button type="button" wire:click="previousStep" class="btn-ghost">Back</button>
        @endif

        @if ($step < 3)
            <button type="button" wire:click="nextStep" class="btn-primary ml-auto">Continue</button>
        @else
            <button type="submit" class="btn-primary ml-auto">
                <span wire:loading.remove wire:target="submit">Request proposal</span>
                <span wire:loading wire:target="submit">Sending…</span>
            </button>
        @endif
    </div>
</form>
