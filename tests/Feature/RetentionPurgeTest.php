<?php

declare(strict_types=1);

use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Models\Registration;
use App\Domain\Scheduling\Models\CourseSchedule;

/**
 * Data retention (spec §22.1).
 *
 * The privacy policy is only true if this command runs: unconverted enquiries
 * lose their personal data after 24 months, bookings after the 7-year window.
 */
function backdate(object $model, string $when): void
{
    $model->forceFill(['created_at' => now()->parse($when)])->saveQuietly();
}

it('anonymises an unconverted corporate inquiry older than 24 months', function () {
    $old = CorporateInquiry::create([
        'company_name' => 'Acme BV', 'contact_name' => 'Jane Doe',
        'email' => 'jane@acme.example', 'phone' => '+31 6 12345678',
        'participants' => 8, 'status' => LeadStatus::Contacted, 'message' => 'Please quote',
    ]);
    backdate($old, '-30 months');

    $this->artisan('academia:purge-expired-leads')->assertSuccessful();

    $old->refresh();
    expect($old->contact_name)->toBe('Erased')
        ->and($old->email)->toEndWith('@invalid')
        ->and($old->phone)->toBeNull()
        ->and($old->message)->toBeNull();
});

it('keeps a converted (Won) inquiry even when it is old', function () {
    $won = CorporateInquiry::create([
        'company_name' => 'Won Ltd', 'contact_name' => 'Paid Customer',
        'email' => 'buyer@won.example', 'participants' => 12, 'status' => LeadStatus::Won,
    ]);
    backdate($won, '-30 months');

    $this->artisan('academia:purge-expired-leads')->assertSuccessful();

    expect($won->refresh()->contact_name)->toBe('Paid Customer')
        ->and($won->email)->toBe('buyer@won.example');
});

it('leaves a recent enquiry untouched', function () {
    $recent = IndividualLead::create([
        'name' => 'Fresh Lead', 'email' => 'fresh@example.com', 'source' => 'course_interest',
    ]);
    backdate($recent, '-3 months');

    $this->artisan('academia:purge-expired-leads')->assertSuccessful();

    expect($recent->refresh()->name)->toBe('Fresh Lead')
        ->and($recent->email)->toBe('fresh@example.com');
});

it('anonymises a booking past the 7-year statutory window but keeps its financials', function () {
    $schedule = CourseSchedule::factory()->create(['starts_at' => now()->subYears(8)]);

    $booking = Registration::create([
        'course_schedule_id' => $schedule->id,
        'name' => 'Old Attendee', 'email' => 'attendee@old.example',
        'phone' => '+31 6 99999999', 'company' => 'Old Corp',
        'price_paid_cents' => 245000, 'seats' => 1,
    ]);
    backdate($booking, '-8 years');

    $this->artisan('academia:purge-expired-leads')->assertSuccessful();

    $booking->refresh();
    expect($booking->name)->toBe('Erased')
        ->and($booking->email)->toEndWith('@invalid')
        ->and($booking->company)->toBeNull()
        ->and($booking->price_paid_cents)->toBe(245000); // financial record survives
});

it('is idempotent — a second run does not re-touch anonymised rows', function () {
    $old = IndividualLead::create([
        'name' => 'Gone', 'email' => 'gone@example.com', 'source' => 'course_interest',
    ]);
    backdate($old, '-30 months');

    $this->artisan('academia:purge-expired-leads')->assertSuccessful();
    $firstEmail = $old->refresh()->email;

    $this->artisan('academia:purge-expired-leads')
        ->expectsOutputToContain('individual leads: 0')
        ->assertSuccessful();

    expect($old->refresh()->email)->toBe($firstEmail);
});
