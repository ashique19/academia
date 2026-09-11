<?php

declare(strict_types=1);

namespace App\Domain\Leads\Services;

use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Leads\Models\CorporateInquiry;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CorporateInquiryService
{
    public function create(array $attributes): CorporateInquiry
    {
        return DB::transaction(function () use ($attributes): CorporateInquiry {
            $inquiry = CorporateInquiry::create([
                ...$attributes,
                'status'                => LeadStatus::New,
                'first_response_due_at' => $this->responseDueAt(),
            ]);

            $inquiry->statusChanges()->create([
                'from_status' => null,
                'to_status'   => LeadStatus::New,
                'note'        => 'Enquiry received from the website.',
                'created_at'  => now(),
            ]);

            return $inquiry;
        });
    }

    /**
     * The SLA deadline, counted in BUSINESS hours.
     *
     * A promise of "within two hours" made at 17:30 on a Friday and measured
     * in wall-clock time is a promise that is broken before anyone is awake.
     * Counting business hours is what makes the number on the corporate page
     * something the team can actually hit.
     */
    public function responseDueAt(?Carbon $from = null): Carbon
    {
        $hours    = (int) config('academia.leads.sla_hours', 2);
        $start    = (int) config('academia.leads.business_hours.start', 8);
        $end      = (int) config('academia.leads.business_hours.end', 18);
        $workDays = config('academia.leads.business_days', [1, 2, 3, 4, 5]);

        $cursor    = ($from ?? now())->copy();
        $remaining = $hours;

        while ($remaining > 0) {
            if (! in_array($cursor->dayOfWeekIso, $workDays, true)) {
                $cursor->addDay()->setTime($start, 0);
                continue;
            }

            if ($cursor->hour < $start) {
                $cursor->setTime($start, 0);
            }

            if ($cursor->hour >= $end) {
                $cursor->addDay()->setTime($start, 0);
                continue;
            }

            $hoursLeftToday = $end - $cursor->hour;
            $consume        = min($remaining, $hoursLeftToday);

            $cursor->addHours($consume);
            $remaining -= $consume;
        }

        return $cursor;
    }

    /**
     * Move a lead through the pipeline, writing history.
     *
     * Won requires a value and Lost requires a reason — both enforced here
     * rather than in a form request, so every path (admin, API, command)
     * obeys them. "Why did we lose it" is the most useful field nobody adds.
     */
    public function transitionTo(
        CorporateInquiry $inquiry,
        LeadStatus $to,
        ?User $user = null,
        ?string $note = null,
        ?int $valueCents = null,
        ?string $lostReason = null,
    ): CorporateInquiry {
        if ($to->requiresValue() && $valueCents === null && $inquiry->won_value_cents === null) {
            throw new InvalidArgumentException(
                'Marking an inquiry Won requires the won value — otherwise conversion '
                . 'reporting is a count rather than a number.'
            );
        }

        if ($to->requiresReason() && blank($lostReason) && blank($inquiry->lost_reason)) {
            throw new InvalidArgumentException(
                'Marking an inquiry Lost requires a reason.'
            );
        }

        return DB::transaction(function () use ($inquiry, $to, $user, $note, $valueCents, $lostReason): CorporateInquiry {
            $from = $inquiry->status;

            $inquiry->statusChanges()->create([
                'user_id'     => $user?->id,
                'from_status' => $from,
                'to_status'   => $to,
                'note'        => $note,
                'created_at'  => now(),
            ]);

            $inquiry->update(array_filter([
                'status'          => $to,
                'won_value_cents' => $valueCents,
                'lost_reason'     => $lostReason,
                // The first move off New stops the SLA clock.
                'first_responded_at' => $inquiry->first_responded_at
                    ?? ($from === LeadStatus::New ? now() : null),
            ], fn ($value) => $value !== null));

            return $inquiry->refresh();
        });
    }

    public function assign(CorporateInquiry $inquiry, User $assignee, ?User $by = null): CorporateInquiry
    {
        $inquiry->update(['assigned_to' => $assignee->id]);

        $inquiry->statusChanges()->create([
            'user_id'     => $by?->id,
            'from_status' => $inquiry->status,
            'to_status'   => $inquiry->status,
            'note'        => "Assigned to {$assignee->name}.",
            'created_at'  => now(),
        ]);

        return $inquiry->refresh();
    }
}
