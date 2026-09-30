<?php

declare(strict_types=1);

namespace App\Livewire\Public;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Services\CourseOutlinePdf;
use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Services\SpamGuard;
use App\Mail\CourseOutlineMail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Email-gated course outline. One PDF, no sales sequence.
 *
 * The lead is stored as an IndividualLead (source brochure_download) so it
 * shows up with the other enquiries. The PDF is attached to the email and
 * also offered as a short-lived signed link, because staging mail may be
 * log-only and a manager still needs the file.
 */
class OutlineDownloadForm extends Component
{
    private const LINK_DAYS = 14;

    #[Locked]
    public int $courseId;

    public string $email = '';

    public string $website = '';

    public int $renderedAt = 0;

    public function mount(Course $course): void
    {
        $this->courseId = $course->id;
        $this->renderedAt = now()->timestamp;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:180'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'email.required' => 'Please enter your work email.',
            'email.email' => 'That email address does not look right.',
        ];
    }

    public function submit(SpamGuard $guard, CourseOutlinePdf $outlines): void
    {
        if ($guard->looksAutomated($this->website, $this->renderedAt)) {
            $this->redirectRoute('thank-you', ['type' => 'brochure'], navigate: false);

            return;
        }

        $throttleKey = 'outline-download:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many outline requests from this connection. Please email us at :email.', [
                    'email' => config('academia.email'),
                ]),
            ]);
        }

        $this->validate();
        RateLimiter::hit($throttleKey, decaySeconds: 3600);

        $course = Course::query()->published()->findOrFail($this->courseId);

        $lead = IndividualLead::create([
            'name' => 'Outline download',
            'email' => $this->email,
            'course_id' => $course->id,
            'message' => 'Outline requested for '.$course->display_title,
            'source' => LeadSource::BrochureDownload,
            'status' => 'new',
            'utm_source' => session('attribution.utm_source'),
            'utm_medium' => session('attribution.utm_medium'),
            'utm_campaign' => session('attribution.utm_campaign'),
            'consented_at' => now(),
            'consent_ip' => request()->ip(),
            'consent_version' => config('academia.leads.consent_version'),
        ]);

        $downloadUrl = URL::temporarySignedRoute(
            'courses.outline',
            now()->addDays(self::LINK_DAYS),
            ['course' => $course],
        );

        try {
            Mail::to($lead->email)->send(new CourseOutlineMail(
                $course,
                $downloadUrl,
                $outlines->render($course),
                $outlines->filename($course),
            ));
        } catch (\Throwable $exception) {
            Log::warning('Course outline email failed', [
                'course_id' => $course->id,
                'lead_id' => $lead->id,
                'error' => $exception->getMessage(),
            ]);
        }

        session()->flash('outline_download_url', $downloadUrl);

        $this->redirectRoute('thank-you', ['type' => 'brochure'], navigate: false);
    }

    public function render(): View
    {
        return view('livewire.public.outline-download-form');
    }
}
