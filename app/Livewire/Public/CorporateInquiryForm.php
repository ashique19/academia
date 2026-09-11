<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Leads\Services\CorporateInquiryService;
use App\Domain\Leads\Services\SpamGuard;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * The three-step corporate enquiry.
 *
 * This is the highest-revenue component on the site, and four decisions in it
 * are deliberate:
 *
 *  1. STEP ORDER. Need first, company second, contact last. Participants and
 *     delivery mode are the two fields that decide whether a lead is worth a
 *     call, and they are the least likely to be abandoned. Never open with
 *     "Budget".
 *
 *  2. STATE SURVIVES STEPPING BACK. Livewire holds it server-side, so a
 *     mistyped email on step 3 does not lose steps 1 and 2.
 *
 *  3. SPAM CHECKS FAIL SILENTLY. A bot told it was detected adapts; a bot
 *     that believes it succeeded does not. No CAPTCHA — it measurably costs
 *     conversion on exactly this kind of B2B form.
 *
 *  4. SUBMISSION REDIRECTS rather than swapping in a success message, so the
 *     thank-you page is a real URL an analytics conversion can fire on.
 */
class CorporateInquiryForm extends Component
{
    public int $step = 1;

    // Step 1 — the need
    public ?int $deliveryModeId = null;
    public ?int $participants = null;
    public string $topic = '';
    public ?int $courseId = null;
    public string $preferredStartDate = '';
    public string $preferredWindow = '';

    // Step 2 — the company
    public string $companyName = '';
    public ?int $countryId = null;
    public ?int $cityId = null;
    public string $sector = '';
    public string $companySize = '';

    // Step 3 — the contact
    public string $contactName = '';
    public string $jobTitle = '';
    public string $email = '';
    public string $phone = '';
    public string $budgetRange = '';
    public string $message = '';
    public bool $consent = false;

    /** Honeypot. Hidden from humans; bots fill it. Must stay empty. */
    public string $website = '';

    /** Timing check. Bots submit in under a second. */
    public int $renderedAt = 0;

    private const STEP_FIELDS = [
        1 => ['deliveryModeId', 'participants', 'topic', 'preferredStartDate'],
        2 => ['companyName', 'countryId', 'cityId', 'sector', 'companySize'],
        3 => ['contactName', 'jobTitle', 'email', 'phone', 'budgetRange', 'message', 'consent'],
    ];

    public function mount(?Course $course = null, ?City $city = null): void
    {
        $this->renderedAt = now()->timestamp;

        // Pre-fill when arriving from a course page or a city page, so the
        // buyer is not retyping context the site already knows.
        if ($course?->exists) {
            $this->courseId = $course->id;
            $this->topic    = $course->title;
        }

        if ($city?->exists) {
            $this->cityId    = $city->id;
            $this->countryId = $city->country_id;
        }
    }

    public function rules(): array
    {
        return [
            'deliveryModeId'     => ['required', 'integer', 'exists:delivery_modes,id'],
            'participants'       => ['required', 'integer', 'min:1', 'max:5000'],
            'topic'              => ['required', 'string', 'max:240'],
            'courseId'           => ['nullable', 'integer', 'exists:courses,id'],
            'preferredStartDate' => ['nullable', 'date', 'after_or_equal:today'],
            'preferredWindow'    => ['nullable', 'string', 'max:60'],

            'companyName'        => ['required', 'string', 'max:180'],
            'countryId'          => ['required', 'integer', 'exists:countries,id'],
            'cityId'             => ['nullable', 'integer', 'exists:cities,id'],
            'sector'             => ['nullable', 'string', 'max:80'],
            'companySize'        => ['nullable', 'string', 'max:40'],

            'contactName'        => ['required', 'string', 'max:120'],
            'jobTitle'           => ['nullable', 'string', 'max:120'],
            'email'              => ['required', 'email:rfc', 'max:180'],
            'phone'              => ['nullable', 'string', 'max:32'],
            'budgetRange'        => ['nullable', 'string', 'max:40'],
            'message'            => ['nullable', 'string', 'max:4000'],
            'consent'            => ['accepted'],
        ];
    }

    protected function messages(): array
    {
        return [
            'consent.accepted'     => 'Please confirm you are happy for us to contact you about this enquiry.',
            'participants.required' => 'Roughly how many people need the training? An estimate is fine.',
            'email.email'          => 'That email address does not look right — we need it to send the proposal.',
        ];
    }

    public function nextStep(): void
    {
        $this->validate($this->rulesForStep($this->step));

        $this->step = min(3, $this->step + 1);
    }

    public function previousStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function submit(CorporateInquiryService $inquiries, SpamGuard $guard): void
    {
        // Silent discard. The visitor sees exactly what a human sees.
        if ($guard->looksAutomated($this->website, $this->renderedAt)) {
            $this->redirectRoute('thank-you', ['type' => 'corporate'], navigate: false);

            return;
        }

        $throttleKey = 'corporate-inquiry:' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 3)) {
            throw ValidationException::withMessages([
                'email' => __('We have had several enquiries from this connection in the last hour. '
                    . 'Please call us on :phone and we will take the details directly.',
                    ['phone' => config('academia.phone')]),
            ]);
        }

        $this->validate();

        RateLimiter::hit($throttleKey, decaySeconds: 3600);

        $inquiries->create([
            'company_name'         => $this->companyName,
            'sector'               => $this->sector ?: null,
            'company_size'         => $this->companySize ?: null,
            'country_id'           => $this->countryId,
            'city_id'              => $this->cityId,
            'contact_name'         => $this->contactName,
            'job_title'            => $this->jobTitle ?: null,
            'email'                => $this->email,
            'phone'                => $this->phone ?: null,
            'participants'         => $this->participants,
            'delivery_mode_id'     => $this->deliveryModeId,
            'course_id'            => $this->courseId,
            'topic'                => $this->topic,
            'preferred_start_date' => $this->preferredStartDate ?: null,
            'preferred_window'     => $this->preferredWindow ?: null,
            'budget_range'         => $this->budgetRange ?: null,
            'message'              => $this->message ?: null,
            'source'               => session('attribution.source', 'direct'),
            'utm_source'           => session('attribution.utm_source'),
            'utm_medium'           => session('attribution.utm_medium'),
            'utm_campaign'         => session('attribution.utm_campaign'),
            // GDPR Art. 7: consent must be demonstrable. Storing WHICH wording
            // was agreed to is what makes the record defensible later.
            'consented_at'         => now(),
            'consent_ip'           => request()->ip(),
            'consent_version'      => config('academia.leads.consent_version'),
        ]);

        $this->redirectRoute('thank-you', ['type' => 'corporate'], navigate: false);
    }

    /** Warn on a free provider — never block. Small companies use Gmail. */
    #[Computed]
    public function emailWarning(): ?string
    {
        if ($this->email === '' || ! app(SpamGuard::class)->isFreeEmailProvider($this->email)) {
            return null;
        }

        return __('A work address helps us route this to the right consultant, but this is fine too.');
    }

    #[Computed]
    public function deliveryModes()
    {
        return DeliveryMode::whereIn('slug', ['onsite', 'online', 'classroom'])->orderBy('sort_order')->get();
    }

    #[Computed]
    public function countries()
    {
        return Country::active()->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function citiesInCountry()
    {
        return $this->countryId
            ? City::active()->where('country_id', $this->countryId)->orderBy('name')->get(['id', 'name'])
            : collect();
    }

    private function rulesForStep(int $step): array
    {
        return array_intersect_key($this->rules(), array_flip(self::STEP_FIELDS[$step] ?? []));
    }

    public function render(): View
    {
        return view('livewire.public.corporate-inquiry-form');
    }
}
