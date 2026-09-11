<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Exceptions;

use App\Domain\Scheduling\Models\CourseSchedule;
use RuntimeException;

class SessionNotBookableException extends RuntimeException
{
    public function __construct(public readonly CourseSchedule $schedule)
    {
        parent::__construct(sprintf(
            'Session %s is not bookable (status: %s).',
            $schedule->reference,
            $schedule->status->value
        ));
    }

    public function userMessage(): string
    {
        return __('That date is no longer available to book. Please choose another.');
    }
}
