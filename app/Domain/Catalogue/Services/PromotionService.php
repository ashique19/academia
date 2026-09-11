<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Services;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Content\Models\Promotion;
use App\Domain\Shared\ValueObjects\Discount;
use App\Domain\Shared\ValueObjects\PriceBreakdown;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * The single source of truth for every price the public sees.
 *
 * ---------------------------------------------------------------------------
 * THREE RULES, ENFORCED HERE RATHER THAN IN A POLICY DOCUMENT
 *
 * 1. Every promotion has a named reason and an end date.
 *    `active()` returns null the moment ends_at passes, so an expired
 *    campaign disappears from the banner, the cards, the offers page and the
 *    schema at the same instant. The data model cannot express a permanent
 *    sale — `reason` and `ends_at` are NOT NULL columns.
 *
 * 2. List prices are never inflated to manufacture a discount.
 *    This is also law: Directive (EU) 2019/2161 requires a reduction to be
 *    announced against the lowest price applied in the previous 30 days.
 *    `prior_price_cents` records it, and PriceBreakdown::referencePriceCents()
 *    always shows the lower of the two.
 *
 * 3. Stacking stops at a published ceiling (default 30%).
 *    Applied to the COMBINED figure, so a 20% campaign plus a 25% group rate
 *    cannot silently become 45%. The ceiling is printed on /offers so a buyer
 *    can check that it holds.
 * ---------------------------------------------------------------------------
 */
class PromotionService
{
    /** The campaign running right now, or null. Expiry is not optional. */
    public function active(): ?Promotion
    {
        return Promotion::query()
            ->active()
            ->where('type', 'campaign')
            ->orderByDesc('percentage')
            ->first();
    }

    /** The active campaign, if it applies to this particular course. */
    public function activeFor(Course $course): ?Promotion
    {
        $promotion = $this->active();

        if (! $promotion) {
            return null;
        }

        $categorySlug = $course->subcategory?->category?->slug;

        return $promotion->appliesToCategory($categorySlug) ? $promotion : null;
    }

    /**
     * Compute the full price picture for a course.
     *
     * @param  int          $seats     Basket quantity — drives the group tier.
     * @param  Carbon|null  $startsAt  Session start — drives early booking.
     */
    public function priceFor(Course $course, int $seats = 1, ?Carbon $startsAt = null): PriceBreakdown
    {
        $list = $course->price_cents;
        $cap  = (int) config('academia.promotions.max_stack_percent', 30);

        /** @var Collection<int, Discount> $components */
        $components = collect();

        if ($promotion = $this->activeFor($course)) {
            $components->push(new Discount(
                'campaign', $promotion->name, (int) $promotion->percentage, $promotion->code
            ));
        }

        if ($tier = $this->groupTierFor($seats)) {
            $components->push(new Discount(
                'group', "{$seats} seats", $tier
            ));
        }

        if ($earlyBird = $this->earlyBirdFor($startsAt)) {
            $components->push($earlyBird);
        }

        $requested = (int) $components->sum('percentage');
        $applied   = min($requested, $cap);

        return new PriceBreakdown(
            listPriceCents:  $list,
            priorPriceCents: $course->prior_price_cents,
            discountPercent: $applied,
            finalPriceCents: $list === null ? null : $this->applyPercentage($list, $applied),
            components:      $components,
            wasCapped:       $requested > $cap,
            capPercent:      $cap,
            currency:        $course->currency ?? 'EUR',
        );
    }

    /** The group-booking percentage for a basket quantity, or null. */
    public function groupTierFor(int $seats): ?int
    {
        $applicable = null;

        foreach (config('academia.promotions.group_tiers', []) as $tier) {
            if ($seats >= $tier['min_seats']) {
                $applicable = (int) $tier['percent'];
            }
        }

        return $applicable;
    }

    public function earlyBirdFor(?Carbon $startsAt): ?Discount
    {
        if ($startsAt === null) {
            return null;
        }

        $config = config('academia.promotions.early_bird');

        return now()->diffInDays($startsAt, false) >= $config['days']
            ? new Discount('early', 'Early booking', (int) $config['percent'], $config['code'])
            : null;
    }

    /**
     * Percentages are ADDED, not compounded.
     *
     * A buyer reading "15% + 10%" means 25%. A checkout that quietly
     * compounds to 23.5% is the kind of small dishonesty this whole class
     * exists to prevent — so the arithmetic matches the sentence.
     */
    public function applyPercentage(int $cents, int $percent): int
    {
        if ($percent <= 0) {
            return $cents;
        }

        $discounted = $cents * (100 - $percent) / 100;

        return $this->roundToNearest((int) round($discounted));
    }

    /**
     * Round to the nearest €5.
     *
     * A price ending in 5 reads as a considered number; .99 reads as a nudge,
     * and this brand does not nudge.
     */
    public function roundToNearest(int $cents): int
    {
        $step = (int) config('academia.promotions.round_to_cents', 500);

        if ($step <= 1) {
            return $cents;
        }

        return (int) (round($cents / $step) * $step);
    }

    /** @return array<int, array{min_seats:int, percent:int}> */
    public function groupTiers(): array
    {
        return config('academia.promotions.group_tiers', []);
    }

    public function maxStackPercent(): int
    {
        return (int) config('academia.promotions.max_stack_percent', 30);
    }
}
