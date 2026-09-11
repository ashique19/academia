<?php

declare(strict_types=1);

namespace App\Domain\Leads\Enums;

enum RegistrationStatus: string
{
    /** MVP default: interest registered, no payment taken. */
    case Interest = 'interest';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Attended = 'attended';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Interest => 'Interest registered',
            self::Pending => 'Pending payment',
            self::Confirmed => 'Confirmed',
            self::Cancelled => 'Cancelled',
            self::Attended => 'Attended',
            self::NoShow => 'No show',
        };
    }

    /** Statuses that occupy a seat. Cancelled releases it. */
    public function holdsSeat(): bool
    {
        return in_array($this, [
            self::Interest, self::Pending, self::Confirmed, self::Attended,
        ], true);
    }
}
