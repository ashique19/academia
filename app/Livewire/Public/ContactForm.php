<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Services\SpamGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

/**
 * General contact enquiry — the public /contact page form.
 *
 * Writes an IndividualLead with source=Contact so sales can triage it alongside
 * course interest without a separate inbox.
 */
class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public string $subject = '';

    public string $message = '';

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
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:180'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:4000'],
            'consent' => ['accepted'],
        ];
    }

    public function submit(SpamGuard $guard): void
    {
        if ($guard->looksAutomated($this->website, $this->renderedAt)) {
            $this->redirectRoute('thank-you', ['type' => 'contact'], navigate: false);

            return;
        }

        $throttleKey = 'contact:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many messages from this connection. Please email us at :email.', [
                    'email' => config('academia.email'),
                ]),
            ]);
        }

        $this->validate();
        RateLimiter::hit($throttleKey, decaySeconds: 3600);

        $companyLine = $this->company !== '' ? "Company: {$this->company}\n" : '';
        $body = trim($companyLine."Subject: {$this->subject}\n\n{$this->message}");

        IndividualLead::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'message' => $body,
            'source' => LeadSource::Contact,
            'status' => 'new',
            'utm_source' => session('attribution.utm_source'),
            'utm_medium' => session('attribution.utm_medium'),
            'utm_campaign' => session('attribution.utm_campaign'),
            'consented_at' => now(),
            'consent_ip' => request()->ip(),
            'consent_version' => config('academia.leads.consent_version'),
        ]);

        $this->redirectRoute('thank-you', ['type' => 'contact'], navigate: false);
    }

    public function render(): View
    {
        return view('livewire.public.contact-form');
    }
}
