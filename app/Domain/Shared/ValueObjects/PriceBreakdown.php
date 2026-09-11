<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Illuminate\Support\Collection;

/**
 * The single answer to "what does this course cost right now?"
 *
 * Returned to the course card, the course page, the booking widget, the
 * offers page and the JSON-LD builder, so all five are physically incapable
 * of disagreeing. A discounted price on the page that differs from the price
 * in Course schema is a Google policy violation, not merely untidy.
 */
final readonly class PriceBreakdown
{
    /** @param Collection<int, Discount> $components */
    public function __construct(
        public ?int $listPriceCents,
        public ?int $priorPriceCents,
        public int $discountPercent,
        public ?int $finalPriceCents,
        public Collection $components,
        public bool $wasCapped,
        public int $capPercent,
        public string $currency = 'EUR',
    ) {}

    public function hasDiscount(): bool
    {
        return $this->discountPercent > 0 && $this->finalPriceCents !== null;
    }

    public function requiresQuote(): bool
    {
        return $this->listPriceCents === null;
    }

    public function savingCents(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        return ($this->listPriceCents ?? 0) - ($this->finalPriceCents ?? 0);
    }

    /**
     * The figure a discount must be announced against.
     *
     * Under EU Directive 2019/2161 this is the lowest price applied in the
     * previous 30 days — not simply today's list price. Showing the higher
     * of the two would be exactly the practice the directive prohibits.
     */
    public function referencePriceCents(): ?int
    {
        if ($this->priorPriceCents === null) {
            return $this->listPriceCents;
        }

        return min($this->priorPriceCents, $this->listPriceCents ?? PHP_INT_MAX);
    }

    public function toArray(): array
    {
        return [
            'list_price_cents' => $this->listPriceCents,
            'reference_cents' => $this->referencePriceCents(),
            'final_price_cents' => $this->finalPriceCents,
            'discount_percent' => $this->discountPercent,
            'saving_cents' => $this->savingCents(),
            'was_capped' => $this->wasCapped,
            'cap_percent' => $this->capPercent,
            'components' => $this->components->map->toArray()->all(),
            'currency' => $this->currency,
        ];
    }
}
