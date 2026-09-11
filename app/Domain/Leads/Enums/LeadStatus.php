<?php

declare(strict_types=1);

namespace App\Domain\Leads\Enums;

/**
 * Corporate inquiry pipeline (spec §9.4).
 *
 * Every transition writes a lead_status_changes row. The current status is
 * never overwritten without recording who moved it and when — otherwise
 * conversion reporting is a snapshot with no history behind it.
 */
enum LeadStatus: string
{
    case New              = 'new';
    case Contacted        = 'contacted';
    case ProposalRequired = 'proposal_required';
    case ProposalSent     = 'proposal_sent';
    case Negotiation      = 'negotiation';
    case Won              = 'won';
    case Lost             = 'lost';
    case Closed           = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New              => 'New',
            self::Contacted        => 'Contacted',
            self::ProposalRequired => 'Proposal required',
            self::ProposalSent     => 'Proposal sent',
            self::Negotiation      => 'Negotiation',
            self::Won              => 'Won',
            self::Lost             => 'Lost',
            self::Closed           => 'Closed',
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Won, self::Lost, self::Closed], true);
    }

    /** Won requires a value; Lost requires a reason. Enforced in the service. */
    public function requiresValue(): bool
    {
        return $this === self::Won;
    }

    public function requiresReason(): bool
    {
        return $this === self::Lost;
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::New                                    => 'orange',
            self::Contacted, self::ProposalRequired      => 'gold',
            self::ProposalSent, self::Negotiation        => 'gold',
            self::Won                                    => 'green',
            self::Lost, self::Closed                     => 'neutral',
        };
    }

    /** Pipeline board column order. */
    public function sortOrder(): int
    {
        return match ($this) {
            self::New => 1, self::Contacted => 2, self::ProposalRequired => 3,
            self::ProposalSent => 4, self::Negotiation => 5,
            self::Won => 6, self::Lost => 7, self::Closed => 8,
        };
    }
}
