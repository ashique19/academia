<?php

declare(strict_types=1);

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Services\CourseOutlinePdf;
use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Livewire\Public\ContactForm;
use App\Livewire\Public\CorporateInquiryForm;
use App\Livewire\Public\OutlineDownloadForm;
use App\Mail\CourseOutlineMail;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

it('shows the outline lead magnet on a course page and emails a pdf', function () {
    Mail::fake();

    $course = Course::factory()->create([
        'title' => 'Leadership Essentials for New Managers',
        'slug' => 'leadership-essentials-for-new-managers',
        'summary' => 'A one-day foundation for new managers.',
        'learning_objectives' => ['Explain the principles of first-time leadership'],
        'prerequisites' => 'None. This course starts from first principles.',
        'price_cents' => 69500,
    ]);

    $course->modules()->create([
        'title' => 'Framing the subject',
        'bullets' => ['Terminology and the mental model'],
        'sort_order' => 1,
    ]);

    CourseSchedule::factory()->create([
        'course_id' => $course->id,
        'starts_at' => now()->addMonth()->setTime(9, 0),
    ]);

    $this->get(route('courses.show', $course))
        ->assertOk()
        ->assertSee('Take the full outline with you', false)
        ->assertSee('Send me the outline', false)
        ->assertSee('One email with the PDF. No sales sequence unless you ask for one.', false)
        ->assertSee('ac-outline-email', false);

    $html = app(CourseOutlinePdf::class)->html($course->fresh());
    expect($html)
        ->toContain('Leadership Essentials for New Managers')
        ->toContain('Framing the subject')
        ->toContain('Explain the principles of first-time leadership')
        ->toContain('€695');

    $component = Livewire::test(OutlineDownloadForm::class, ['course' => $course])
        ->set('email', 'buyer@example.com')
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->call('submit')
        ->assertHasNoErrors();

    $lead = IndividualLead::query()->first();
    expect($lead)->not->toBeNull()
        ->and($lead->source)->toBe(LeadSource::BrochureDownload)
        ->and($lead->email)->toBe('buyer@example.com')
        ->and($lead->course_id)->toBe($course->id);

    $component->assertRedirect('/thank-you/brochure?lead='.$lead->uuid);

    Mail::assertSent(CourseOutlineMail::class, function (CourseOutlineMail $mail) use ($course): bool {
        $mail->assertSeeInHtml('<html', false);
        $mail->assertSeeInHtml('<body', false);
        $mail->assertSeeInText('is attached as a PDF.');
        $mail->assertSeeInText('No sales sequence unless you ask for one.');

        return $mail->hasTo('buyer@example.com')
            && $mail->filename === $course->slug.'-outline.pdf'
            && str_starts_with($mail->pdf, '%PDF')
            && str_contains($mail->render(), '<html')
            && ($mail->headers()->text['List-Unsubscribe'] ?? null) === '<mailto:info@academiatraining.eu>';
    });
});

it('sends the outline as multipart html and plain text with list-unsubscribe', function () {
    $course = Course::factory()->create([
        'title' => 'Leadership Essentials for New Managers',
        'slug' => 'leadership-outline-mail',
    ]);

    $downloadUrl = 'https://example.test/courses/leadership-outline-mail/outline.pdf?signature=abc&expires=1';

    Mail::to('buyer@example.com')->send(new CourseOutlineMail(
        $course,
        $downloadUrl,
        "%PDF-1.4\n",
        $course->slug.'-outline.pdf',
    ));

    $message = app('mailer')->getSymfonyTransport()->messages()->first()->getOriginalMessage();
    $raw = $message->toString();

    expect($message->getHtmlBody())->toContain('<html')
        ->and($message->getTextBody())->toContain($downloadUrl)
        ->and($message->getTextBody())->toContain('Leadership Essentials for New Managers')
        ->and($raw)->toContain('text/plain')
        ->and($raw)->toContain('text/html')
        ->and($raw)->toContain('List-Unsubscribe: <mailto:info@academiatraining.eu>');
});

it('rejects an outline download without a signature and serves a signed one', function () {
    $course = Course::factory()->create(['slug' => 'outline-download-course', 'price_cents' => 100000]);

    $this->get(route('courses.outline', $course))->assertForbidden();

    $url = URL::temporarySignedRoute('courses.outline', now()->addHour(), ['course' => $course]);

    $this->get($url)
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('does not offer an outline pdf for an unpublished course', function () {
    $course = Course::factory()->draft()->create(['slug' => 'draft-outline-course']);

    $url = URL::temporarySignedRoute('courses.outline', now()->addHour(), ['course' => $course]);

    $this->get($url)->assertNotFound();
});

it('shows the outline download from the lead on the redirect, without session', function () {
    $course = Course::factory()->create([
        'slug' => 'outline-thank-you-course',
        'price_cents' => 100000,
    ]);

    $lead = IndividualLead::query()->create([
        'name' => 'Outline download',
        'email' => 'buyer@example.com',
        'course_id' => $course->id,
        'source' => LeadSource::BrochureDownload,
        'status' => 'new',
    ]);

    $this->flushSession();

    $this->get(route('thank-you', ['type' => 'brochure', 'lead' => $lead->uuid]))
        ->assertOk()
        ->assertSee('Outline on its way', false)
        ->assertSee('Download the PDF', false)
        ->assertSee('/courses/'.$course->slug.'/outline.pdf', false)
        ->assertSee('signature=', false);

    $this->get(route('thank-you', 'brochure'))
        ->assertOk()
        ->assertDontSee('Download the PDF', false);

    $lead->forceFill(['created_at' => now()->subDays(15)])->save();

    $this->get(route('thank-you', ['type' => 'brochure', 'lead' => $lead->uuid]))
        ->assertOk()
        ->assertDontSee('Download the PDF', false);
});

it('keeps a session fallback for the outline download link', function () {
    $this->withSession(['outline_download_url' => 'https://example.test/outline.pdf?signature=test'])
        ->get(route('thank-you', 'brochure'))
        ->assertOk()
        ->assertSee('Download the PDF', false)
        ->assertSee('https://example.test/outline.pdf?signature=test', false);
});

it('does not turn another lead into an outline download', function () {
    $lead = IndividualLead::query()->create([
        'name' => 'Alex',
        'email' => 'alex@example.com',
        'source' => LeadSource::Contact,
        'status' => 'new',
        'message' => 'Hello',
    ]);

    $this->get(route('thank-you', ['type' => 'brochure', 'lead' => $lead->uuid]))
        ->assertOk()
        ->assertDontSee('Download the PDF', false);
});

it('loads dompdf from the production composer require', function () {
    expect(class_exists(Options::class))->toBeTrue()
        ->and(class_exists(Dompdf::class))->toBeTrue();
});

it('shows contact validation instead of a silent empty submit', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('data-validate', false)
        ->assertSee('Please complete the highlighted fields before sending.', false)
        ->assertSee('[wire\\:loading][wire\\:loading]', false);

    Livewire::test(ContactForm::class)
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'message', 'consent'])
        ->assertSee('Please enter your name.')
        ->assertSee('Please enter your work email.')
        ->assertSee('Please add a subject.')
        ->assertSee('Please tell us how we can help.')
        ->assertSee('Please confirm you are happy for us to contact you about this enquiry.')
        ->assertNoRedirect();

    expect(IndividualLead::query()->count())->toBe(0);
});

it('asks for a delivery mode in plain language', function () {
    Livewire::test(CorporateInquiryForm::class)
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->call('nextStep')
        ->assertHasErrors(['deliveryModeId', 'participants', 'topic'])
        ->assertSee('Please choose a delivery mode.')
        ->assertDontSee('delivery mode id');
});

it('describes registration as an enquiry rather than a completed booking', function () {
    $this->get(route('thank-you', 'registration'))
        ->assertOk()
        ->assertSee('Enquiry received', false)
        ->assertSee('Nothing is booked and no payment has been taken.', false)
        ->assertDontSee('You are registered', false)
        ->assertDontSee('joining details', false);
});
