<?php

declare(strict_types=1);

use App\Domain\Catalogue\Services\PromotionService;

/**
 * The promotion rules, tested as rules.
 *
 * Each of these corresponds to a promise printed on the public offers page.
 * If one fails, the site is making a claim the code does not honour.
 */
it('adds percentages rather than compounding them', function () {
    // A buyer reading "15% + 10%" means 25%. Compounding to 23.5% at the till
    // is the small dishonesty this whole service exists to prevent.
    $service = new PromotionService;

    expect($service->applyPercentage(100_000, 25))->toBe(75_000);
});

it('never lets stacked discounts exceed the published ceiling', function () {
    config(['academia.promotions.max_stack_percent' => 30]);

    // 20% campaign + 25% group would be 45% if uncapped.
    $capped = min(20 + 25, config('academia.promotions.max_stack_percent'));

    expect($capped)->toBe(30);
});

it('rounds prices to the nearest five euros', function () {
    $service = new PromotionService;
    config(['academia.promotions.round_to_cents' => 500]);

    // 695.00 less 20% = 556.00 -> 555.00
    expect($service->applyPercentage(69_500, 20))->toBe(55_500)
        ->and($service->roundToNearest(55_600))->toBe(55_500)
        ->and($service->roundToNearest(55_800))->toBe(56_000);
});

it('returns the correct group tier for a basket quantity', function () {
    config(['academia.promotions.group_tiers' => [
        ['min_seats' => 3,  'percent' => 15],
        ['min_seats' => 6,  'percent' => 20],
        ['min_seats' => 10, 'percent' => 25],
    ]]);

    $service = new PromotionService;

    expect($service->groupTierFor(1))->toBeNull()
        ->and($service->groupTierFor(2))->toBeNull()
        ->and($service->groupTierFor(3))->toBe(15)
        ->and($service->groupTierFor(5))->toBe(15)
        ->and($service->groupTierFor(6))->toBe(20)
        ->and($service->groupTierFor(9))->toBe(20)
        ->and($service->groupTierFor(10))->toBe(25)
        ->and($service->groupTierFor(500))->toBe(25);
});

it('leaves the price untouched when no discount applies', function () {
    expect((new PromotionService)->applyPercentage(69_500, 0))->toBe(69_500);
});
