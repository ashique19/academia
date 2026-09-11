<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Exceptions;

use App\Domain\Scheduling\Models\CourseSchedule;
use RuntimeException;

class InsufficientSeatsException extends RuntimeException
{
    public function __construct(
        public readonly CourseSchedule $schedule,
        public readonly int $requested,
        public readonly int $available,
    ) {
        parent::__construct(sprintf(
            'Session %s has %d seat(s) available; %d requested.',
            $schedule->reference,
            $available,
            $requested
        ));
    }

    public function userMessage(): string
    {
        return $this->available === 0
            ? __('That date has just sold out. Join the waitlist and we will tell you when a place frees up.')
            : trans_choice(
                '{1}Only :count seat is left on that date.|[2,*]Only :count seats are left on that date.',
                $this->available,
                ['count' => $this->available]
            );
    }
}
