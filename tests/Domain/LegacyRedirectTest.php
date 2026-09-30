<?php

declare(strict_types=1);

use App\Http\LegacyRedirector;

it('maps the live catalogue, schedule and classroom hubs', function () {
    $redirects = app(LegacyRedirector::class);

    expect($redirects->target('/training-catalogue'))->toBe('/courses')
        ->and($redirects->target('/training-schedule/'))->toBe('/schedule')
        ->and($redirects->target('/classroom-training-europe'))->toBe('/classroom-training');
});

it('maps live category hubs and children onto the 12 public categories', function () {
    $redirects = app(LegacyRedirector::class);

    expect($redirects->target('/training/finance-accounting'))->toBe('/courses/category/finance-accounting')
        ->and($redirects->target('/training/compliance-risk-governance/'))->toBe('/courses/category/compliance-risk-management')
        ->and($redirects->target('/training/technology-digital-skills'))->toBe('/courses')
        ->and($redirects->target('/training/technology-digital-skills/cybersecurity'))
        ->toBe('/courses/category/it-cybersecurity/cybersecurity')
        ->and($redirects->target('/training/business-management/leadership-development'))
        ->toBe('/courses/category/leadership-soft-skills/leadership-development')
        ->and($redirects->target('/training/finance-accounting/not-a-real-topic'))
        ->toBe('/courses?'.http_build_query(['q' => 'not a real topic']));
});

it('maps catalogue query filters', function () {
    $redirects = app(LegacyRedirector::class);

    expect($redirects->target('/training-catalogue', ['cat' => 'compliance-risk-governance']))
        ->toBe('/courses/category/compliance-risk-management')
        ->and($redirects->target('/training-catalogue', ['sub' => 'data-analytics-bi']))
        ->toBe('/courses/category/data-analytics-bi/data-analytics-bi')
        ->and($redirects->target('/training-catalogue', ['q' => 'power bi']))
        ->toBe('/courses?'.http_build_query(['q' => 'power bi']))
        ->and($redirects->target('/training-catalogue', ['cat' => 'technology-digital-skills', 'q' => 'excel']))
        ->toBe('/courses?'.http_build_query(['q' => 'excel']));
});

it('maps the 26 classroom cities and country hubs', function () {
    $redirects = app(LegacyRedirector::class);

    expect($redirects->target('/locations/amsterdam'))->toBe('/classroom-training/netherlands/amsterdam')
        ->and($redirects->target('/locations/prague/'))->toBe('/classroom-training/czechia/prague')
        ->and($redirects->target('/locations/not-a-city'))->toBe('/classroom-training')
        ->and($redirects->target('/training-in/germany'))->toBe('/classroom-training/germany')
        ->and($redirects->target('/training-in/atlantis'))->toBe('/classroom-training');

    $cities = [
        'amsterdam', 'rotterdam', 'utrecht', 'eindhoven', 'berlin', 'munich', 'frankfurt', 'hamburg',
        'paris', 'lyon', 'brussels', 'antwerp', 'madrid', 'barcelona', 'milan', 'rome', 'zurich',
        'geneva', 'vienna', 'dublin', 'copenhagen', 'stockholm', 'warsaw', 'lisbon', 'prague', 'luxembourg',
    ];

    expect($cities)->toHaveCount(26);

    foreach ($cities as $city) {
        expect($redirects->target('/locations/'.$city))->toStartWith('/classroom-training/');
    }
});

it('maps delivery modes', function () {
    $redirects = app(LegacyRedirector::class);

    expect($redirects->target('/delivery/online'))->toBe('/online-training')
        ->and($redirects->target('/delivery/classroom'))->toBe('/classroom-training')
        ->and($redirects->target('/delivery/onsite'))->toBe('/corporate-training')
        ->and($redirects->target('/delivery/self-paced'))->toBe('/courses?mode[0]=self-paced')
        ->and($redirects->target('/delivery/unknown'))->toBe('/courses');
});
