<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Enums;

/**
 * Session lifecycle.
 *
 *   Scheduled ──publish──> Open ──seats full──> Full
 *       │                   │  ^                 │
 *       │                   │  └── cancellation ─┘
 *       └────> Cancelled <──┘
 *                           └── ends_at passes ──> Completed
 *
 * Transitions go through ScheduleState, never by assigning ->status
 * directly, because Full -> Open must free a seat and Cancelled must
 * notify every registrant.
 */
enum ScheduleStatus: string
{
    case Scheduled = 'scheduled';
    case Open = 'open';
    case Full = 'full';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** Only Open sessions can take a booking. */
    public function isBookable(): bool
    {
        return $this === self::Open;
    }

    /** Full sessions still appear publicly — with a waitlist CTA. */
    public function isPubliclyVisible(): bool
    {
        return in_array($this, [self::Open, self::Full], true);
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Open => 'green',
            self::Full => 'gold',
            self::Cancelled => 'red',
            self::Completed => 'neutral',
            self::Scheduled => 'neutral',
        };
    }

    /** @return array<int, self> */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Scheduled => [self::Open, self::Cancelled],
            self::Open => [self::Full, self::Cancelled, self::Completed],
            self::Full => [self::Open, self::Cancelled, self::Completed],
            self::Cancelled => [],
            self::Completed => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
