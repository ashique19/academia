<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Enums;

/**
 * Licensing status of a third-party certification scheme.
 *
 * This single value drives the course title, the trademark notice, the exam
 * block and the schema output. Obtaining an ATO/ATP licence is one enum
 * change — no content editing anywhere.
 *
 * See spec §6.4. Academia currently holds no licence for any scheme, so
 * every matched course is `Independent` and is sold as exam preparation.
 */
enum SchemeStatus: string
{
    /** Exam preparation only. Academia cannot issue the certificate. */
    case Independent = 'independent';

    /** The ATO/ATP licence is on file. Full certification, exam included. */
    case Accredited = 'accredited';

    public function isIndependent(): bool
    {
        return $this === self::Independent;
    }

    public function label(): string
    {
        return match ($this) {
            self::Independent => 'Independent exam preparation',
            self::Accredited => 'Accredited training organisation',
        };
    }
}
