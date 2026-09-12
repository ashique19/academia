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
 * Short callback request — for buyers who will not complete a long enquiry.
 */
class CallbackForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $preferredWindow = 'As soon as possible';

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
            'phone' => ['required', 'string', 'max:32'],
            'preferredWindow' => ['required', 'string', 'max:80'],
            'consent' => ['accepted'],
        ];
    }

    public function submit(SpamGuard $guard): void
    {
        if ($guard->looksAutomated($this->website, $this->renderedAt)) {
            $this->redirectRoute('thank-you', ['type' => 'callback'], navigate: false);

            return;
        }

        $throttleKey = 'callback:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'phone' => __('Too many callback requests from this connection. Please call us on :phone.', [
                    'phone' => config('academia.phone'),
                ]),
            ]);
        }

        $this->validate();
        RateLimiter::hit($throttleKey, decaySeconds: 3600);

        IndividualLead::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => 'Preferred callback window: '.$this->preferredWindow,
            'source' => LeadSource::CallbackRequest,
            'status' => 'new',
            'utm_source' => session('attribution.utm_source'),
            'utm_medium' => session('attribution.utm_medium'),
            'utm_campaign' => session('attribution.utm_campaign'),
            'consented_at' => now(),
            'consent_ip' => request()->ip(),
            'consent_version' => config('academia.leads.consent_version'),
        ]);

        $this->redirectRoute('thank-you', ['type' => 'callback'], navigate: false);
    }

    public function render(): View
    {
        return view('livewire.public.callback-form');
    }
}
