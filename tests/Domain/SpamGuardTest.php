<?php

declare(strict_types=1);

use App\Domain\Leads\Services\SpamGuard;

beforeEach(function () {
    config(['academia.leads.min_form_seconds' => 3]);
    $this->guard = new SpamGuard;
});

it('flags a submission with the honeypot filled', function () {
    expect($this->guard->looksAutomated('http://spam.example', now()->timestamp - 60))->toBeTrue();
});

it('flags a submission made faster than a human could type it', function () {
    expect($this->guard->looksAutomated('', now()->timestamp))->toBeTrue();
});

it('accepts a normal human submission', function () {
    expect($this->guard->looksAutomated('', now()->timestamp - 45))->toBeFalse();
});

it('treats a stripped timestamp as suspicious', function () {
    expect($this->guard->looksAutomated('', 0))->toBeTrue();
});

it('identifies free email providers without blocking them', function () {
    // Warns only. Plenty of legitimate small-company buyers use Gmail, and
    // blocking them to filter noise loses real revenue.
    expect($this->guard->isFreeEmailProvider('someone@gmail.com'))->toBeTrue()
        ->and($this->guard->isFreeEmailProvider('buyer@abnamro.com'))->toBeFalse();
});
