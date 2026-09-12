<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Services\SpamGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * Newsletter signup with double opt-in.
 *
 * Creates an unconfirmed IndividualLead (source=Newsletter). The confirmation
 * link on /newsletter/confirm/{token} sets confirmed_at.
 */
class NewsletterForm extends Component
{
    public string $email = '';

    public string $name = '';

    public bool $consent = false;

    public string $website = '';

    public int $renderedAt = 0;

    public function mount(): void
    {
        $this->renderedAt = now()->timestamp;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:180'],
            'name' => ['nullable', 'string', 'max:120'],
            'consent' => ['accepted'],
        ];
    }

    public function submit(SpamGuard $guard): void
    {
        if ($guard->looksAutomated($this->website, $this->renderedAt)) {
            $this->redirectRoute('thank-you', ['type' => 'newsletter'], navigate: false);

            return;
        }

        $throttleKey = 'newsletter:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many signup attempts from this connection. Please try again later.'),
            ]);
        }

        $this->validate();
        RateLimiter::hit($throttleKey, decaySeconds: 3600);

        IndividualLead::create([
            'name' => $this->name !== '' ? $this->name : null,
            'email' => $this->email,
            'source' => LeadSource::Newsletter,
            'status' => 'new',
            'confirmation_token' => Str::random(64),
            'utm_source' => session('attribution.utm_source'),
            'utm_medium' => session('attribution.utm_medium'),
            'utm_campaign' => session('attribution.utm_campaign'),
            'consented_at' => now(),
            'consent_ip' => request()->ip(),
            'consent_version' => config('academia.leads.consent_version'),
        ]);

        // Confirmation email is out of scope for this port — the thank-you
        // page still explains the double opt-in step, and the confirm route
        // is live for when mail is wired.

        $this->redirectRoute('thank-you', ['type' => 'newsletter'], navigate: false);
    }

    public function render(): View
    {
        return view('livewire.public.newsletter-form');
    }
}
