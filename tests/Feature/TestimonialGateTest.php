<?php

declare(strict_types=1);

use App\Domain\Content\Models\Testimonial;

/**
 * The verification gate lives in an observer, not a form request, so every
 * write path obeys it — admin panel, seeder, import and any future API.
 */

it('refuses to publish an unverified, unflagged testimonial', function () {
    Testimonial::create([
        'quote'  => 'Something nobody actually said.',
        'status' => 'published',
    ]);
})->throws(DomainException::class);

it('publishes a verified testimonial with a consent reference on file', function () {
    $testimonial = Testimonial::create([
        'quote'             => 'A real, permissioned quote.',
        'author_name'       => 'A Real Person',
        'is_verified'       => true,
        'consent_reference' => 'CONSENT-2026-014',
        'status'            => 'published',
    ]);

    expect($testimonial->exists)->toBeTrue();
});

it('refuses to publish a "verified" testimonial with no consent reference', function () {
    // "We asked them" is not evidence; a reference is.
    Testimonial::create([
        'quote'       => 'Claimed as verified but nothing on file.',
        'is_verified' => true,
        'status'      => 'published',
    ]);
})->throws(DomainException::class);

it('publishes an illustrative testimonial and strips any personal attribution', function () {
    $testimonial = Testimonial::create([
        'quote'           => 'The outcome this course is designed to produce.',
        'author_name'     => 'Should Be Removed',
        'organisation'    => 'Should Also Go',
        'is_illustrative' => true,
        'status'          => 'published',
    ]);

    // The marker and the byline must not contradict each other.
    expect($testimonial->author_name)->toBeNull()
        ->and($testimonial->organisation)->toBeNull();
});
