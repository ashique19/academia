<?php

declare(strict_types=1);

use App\Domain\Catalogue\Services\CertificationSchemeMatcher;

/**
 * Regression tests for scheme matching.
 *
 * An earlier version of this product used a naive case-insensitive substring
 * match and tagged "Building Psychological Safety" as a SAFe(R) course —
 * putting a trademark notice and an exam specification on a page that had no
 * business carrying either. These tests exist so that cannot recur.
 */
beforeEach(function () {
    $this->matcher = new CertificationSchemeMatcher;
});

it('does not match SAFe inside the word "Safety"', function () {
    expect($this->matcher->matches('Building Psychological Safety', 'SAFe'))->toBeFalse();
});

it('is case sensitive, because a mark is not an adjective', function () {
    expect($this->matcher->matches('Keeping data safe', 'SAFe'))->toBeFalse()
        ->and($this->matcher->matches('SAFe for Teams', 'SAFe'))->toBeTrue();
});

it('matches a genuine scheme reference at a word boundary', function () {
    expect($this->matcher->matches('PRINCE2 Foundation', 'PRINCE2'))->toBeTrue()
        ->and($this->matcher->matches('ITIL 4 Foundation', 'ITIL'))->toBeTrue();
});

it('matches a needle that ends in punctuation', function () {
    // \b after ")" never matches, so the boundary is applied conditionally.
    expect($this->matcher->matches(
        'Business Continuity Management (ISO 22301)',
        '(ISO 22301)'
    ))->toBeTrue();
});

it('does not match a scheme name embedded in a longer word', function () {
    expect($this->matcher->matches('PRINCE2Xtra Masterclass', 'PRINCE2'))->toBeFalse();
});
