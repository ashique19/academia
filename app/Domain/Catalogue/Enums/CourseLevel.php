<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Enums;

enum CourseLevel: string
{
    case Foundation = 'foundation';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /** Ordering for the "level" facet — never alphabetical. */
    public function sortOrder(): int
    {
        return match ($this) {
            self::Foundation => 1,
            self::Intermediate => 2,
            self::Advanced => 3,
        };
    }

    public static function fromLabel(string $label): self
    {
        return self::from(strtolower(trim($label)));
    }
}
