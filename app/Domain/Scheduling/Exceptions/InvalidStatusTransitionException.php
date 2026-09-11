<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Exceptions;

use App\Domain\Scheduling\Enums\ScheduleStatus;
use DomainException;

class InvalidStatusTransitionException extends DomainException
{
    public function __construct(ScheduleStatus $from, ScheduleStatus $to)
    {
        parent::__construct(sprintf(
            'Cannot move a session from %s to %s.',
            $from->value,
            $to->value
        ));
    }
}
