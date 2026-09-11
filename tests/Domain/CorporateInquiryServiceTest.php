<?php

declare(strict_types=1);

use App\Domain\Leads\Services\CorporateInquiryService;
use Illuminate\Support\Carbon;

/**
 * The SLA clock counts BUSINESS hours.
 *
 * A "within two hours" promise made at 17:30 on a Friday and measured in
 * wall-clock time is broken before anyone is awake. Counting business hours is
 * what makes the number printed on the corporate page something the team can
 * actually hit.
 */

beforeEach(function () {
    config([
        'academia.leads.sla_hours' => 2,
        'academia.leads.business_hours' => ['start' => 8, 'end' => 18],
        'academia.leads.business_days' => [1, 2, 3, 4, 5],
    ]);

    $this->service = new CorporateInquiryService();
});

it('adds two hours during the working day', function () {
    // Wednesday 10:00 -> 12:00 the same day
    $due = $this->service->responseDueAt(Carbon::parse('2026-09-02 10:00'));

    expect($due->format('Y-m-d H:i'))->toBe('2026-09-02 12:00');
});

it('rolls over to the next morning when raised after hours', function () {
    // Wednesday 17:30 -> only 30 minutes of Wednesday remain, so it lands
    // on Thursday morning rather than at 19:30 the same evening.
    $due = $this->service->responseDueAt(Carbon::parse('2026-09-02 17:30'));

    expect($due->isAfter(Carbon::parse('2026-09-03 08:00')))->toBeTrue()
        ->and($due->day)->toBe(3);
});

it('skips the weekend', function () {
    // Friday evening must not become Saturday.
    $due = $this->service->responseDueAt(Carbon::parse('2026-09-04 19:00'));

    expect($due->dayOfWeekIso)->toBeLessThanOrEqual(5);
});
