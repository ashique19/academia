<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Enums;

enum CourseStatus: string
{
    case Draft     = 'draft';
    case Published = 'published';
    case Archived  = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Published => 'Published',
            self::Archived  => 'Archived',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Draft     => 'neutral',
            self::Published => 'green',
            self::Archived  => 'orange',
        };
    }

    public function isPubliclyVisible(): bool
    {
        return $this === self::Published;
    }
}
