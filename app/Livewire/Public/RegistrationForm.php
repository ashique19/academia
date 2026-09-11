<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Services\SpamGuard;
use App\Domain\Scheduling\Exceptions\InsufficientSeatsException;
use App\Domain\Scheduling\Exceptions\SessionNotBookableException;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Scheduling\Services\RegistrationService;
use App\Domain\Shared\Models\Country;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Individual registration of interest.
 *
 * Goes through RegistrationService so the seat reservation is atomic even
 * though MVP takes no payment — retrofitting the locking after checkout
 * exists means auditing every write path that was added in between.
 */
class RegistrationForm extends Component
{
    /** #[Locked] so a crafted request cannot swap the course after render. */
    #[Locked]
    public int $courseId;

    public ?int $scheduleId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $company = '';

    public ?int $countryId = null;

    public string $message = '';

    public bool $consent = false;

    public string $website = '';

    public int $renderedAt = 0;

    public function mount(Course $course, ?CourseSchedule $schedule = null): void
    {
        $this->courseId = $course->id;
        $this->scheduleId = $schedule?->id ?? $course->next_session?->id;
        $this->renderedAt = now()->timestamp;
    }

    public function rules(): array
    {
        return [
            'scheduleId' => ['nullable', 'integer', 'exists:course_schedules,id'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['nullable', 'string', 'max:32'],
            'company' => ['nullable', 'string', 'max:180'],
            'countryId' => ['nullable', 'integer', 'exists:countries,id'],
            'message' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
        ];
    }

    public function submit(RegistrationService $registrations, SpamGuard $guard): void
    {
        if ($guard->looksAutomated($this->website, $this->renderedAt)) {
            $this->redirectRoute('thank-you', ['type' => 'registration'], navigate: false);

            return;
        }

        $throttleKey = 'registration:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many registrations from this connection. Please email us at :email.',
                    ['email' => config('academia.email')]),
            ]);
        }

        $this->validate();
        RateLimiter::hit($throttleKey, decaySeconds: 3600);

        $attributes = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'company' => $this->company ?: null,
            'country_id' => $this->countryId,
            'message' => $this->message ?: null,
            'source' => session('attribution.source', 'direct'),
            'utm_source' => session('attribution.utm_source'),
            'utm_medium' => session('attribution.utm_medium'),
            'utm_campaign' => session('attribution.utm_campaign'),
            'consented_at' => now(),
            'consent_ip' => request()->ip(),
            'consent_version' => config('academia.leads.consent_version'),
        ];

        // No date chosen — capture as a lead rather than losing the visitor.
        if ($this->scheduleId === null) {
            $this->recordLead($attributes);
            $this->redirectRoute('thank-you', ['type' => 'interest'], navigate: false);

            return;
        }

        $schedule = CourseSchedule::findOrFail($this->scheduleId);

        try {
            $registrations->reserve($schedule, $attributes);
        } catch (InsufficientSeatsException|SessionNotBookableException $e) {
            // The seat went while this form was open. Keep the visitor: record
            // the interest, then tell them honestly what happened.
            $this->recordLead($attributes);

            throw ValidationException::withMessages([
                'scheduleId' => $e->userMessage()
                    .' '.__('We have noted your interest and will tell you when a place opens.'),
            ]);
        }

        $this->redirectRoute('thank-you', ['type' => 'registration'], navigate: false);
    }

    private function recordLead(array $attributes): void
    {
        IndividualLead::create([
            ...$attributes,
            'course_id' => $this->courseId,
            'course_schedule_id' => $this->scheduleId,
            'source' => LeadSource::CourseInterest,
        ]);
    }

    #[Computed]
    public function course(): Course
    {
        return Course::findOrFail($this->courseId);
    }

    #[Computed]
    public function schedules()
    {
        return CourseSchedule::query()
            ->where('course_id', $this->courseId)
            ->upcoming()
            ->publiclyVisible()
            ->with(['city', 'deliveryMode'])
            ->orderBy('starts_at')
            ->take(12)
            ->get();
    }

    #[Computed]
    public function countries()
    {
        return Country::active()->orderBy('name')->get(['id', 'name']);
    }

    public function render(): View
    {
        return view('livewire.public.registration-form');
    }
}
