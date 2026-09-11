<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

/** One component of a price reduction — a campaign, a group tier, early booking. */
final readonly class Discount
{
    public function __construct(
        public string $type,
        public string $label,
        public int $percentage,
        public ?string $code = null,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'label' => $this->label,
            'percentage' => $this->percentage,
            'code' => $this->code,
        ];
    }
}
