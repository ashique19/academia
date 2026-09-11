<?php

declare(strict_types=1);

namespace App\Domain\Content\Services;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\ValueObjects\PriceBreakdown;

/**
 * Course + hasCourseInstance JSON-LD.
 *
 * Two rules govern this class:
 *
 *  1. The price in the markup is the price on the page. They come from the
 *     same PriceBreakdown, because a discounted price shown to a human but
 *     not to the crawler (or the reverse) is a mismatch Google treats as a
 *     policy violation rather than an oversight.
 *
 *  2. aggregateRating is emitted ONLY from a verified third-party source with
 *     a count above the configured floor. A self-declared rating in schema is
 *     a manual-action risk and is worth nothing to a corporate buyer who can
 *     see the reviews are hosted by the seller.
 */
class CourseSchemaBuilder
{
    public function build(Course $course, PriceBreakdown $price): array
    {
        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Course',
            '@id'         => route('courses.show', $course) . '#course',
            'name'        => $course->display_title,
            'description' => $course->summary,
            'url'         => route('courses.show', $course),
            'provider'    => [
                '@type' => 'EducationalOrganization',
                'name'  => config('academia.trade_name'),
                'url'   => url('/'),
            ],
            'timeRequired'                 => 'P' . max(1, (int) ceil((float) $course->duration_days)) . 'D',
            'educationalCredentialAwarded' => $this->credential($course),
        ];

        if (filled($course->prerequisites)) {
            $schema['coursePrerequisites'] = $course->prerequisites;
        }

        $instances = $course->schedules
            ->filter(fn (CourseSchedule $s) => $s->status->isPubliclyVisible())
            ->map(fn (CourseSchedule $s) => $this->instance($s, $price))
            ->values()
            ->all();

        if ($instances !== []) {
            $schema['hasCourseInstance'] = $instances;
        }

        if (! $price->requiresQuote()) {
            $schema['offers'] = array_filter([
                '@type'         => 'Offer',
                'category'      => 'Paid',
                'price'         => number_format(($price->finalPriceCents ?? 0) / 100, 2, '.', ''),
                'priceCurrency' => $price->currency,
                'url'           => route('courses.show', $course),
                // validThrough ties the discounted price to the campaign that
                // justifies it, so the markup expires with the offer.
                'validThrough'  => $price->hasDiscount()
                    ? app(\App\Domain\Catalogue\Services\PromotionService::class)
                        ->activeFor($course)?->ends_at?->toIso8601String()
                    : null,
            ], fn ($value) => $value !== null);
        }

        if ($rating = $this->verifiedRating()) {
            $schema['aggregateRating'] = $rating;
        }

        return $schema;
    }

    private function instance(CourseSchedule $session, PriceBreakdown $price): array
    {
        $isOnline = $session->deliveryMode?->slug === 'online';

        return array_filter([
            '@type'          => 'CourseInstance',
            'courseMode'     => $isOnline ? 'online' : 'onsite',
            'startDate'      => $session->starts_at->toIso8601String(),
            'endDate'        => $session->ends_at->toIso8601String(),
            'courseWorkload' => 'P' . max(1, $session->duration_days) . 'D',
            'inLanguage'     => $session->language,
            'location'       => $isOnline
                ? ['@type' => 'VirtualLocation', 'url' => route('courses.show', $session->course)]
                : array_filter([
                    '@type'   => 'Place',
                    'name'    => $session->venue?->name ?? $session->city?->name,
                    'address' => $session->city ? [
                        '@type'           => 'PostalAddress',
                        'addressLocality' => $session->city->name,
                        'addressCountry'  => $session->city->country?->iso2,
                    ] : null,
                ], fn ($v) => $v !== null),
            'offers' => [
                '@type'         => 'Offer',
                'price'         => number_format(($session->effectivePriceCents() ?? 0) / 100, 2, '.', ''),
                'priceCurrency' => $price->currency,
                'availability'  => $session->seats_available > 0
                    ? 'https://schema.org/InStock'
                    : 'https://schema.org/SoldOut',
            ],
        ], fn ($value) => $value !== null);
    }

    /**
     * Certification wording.
     *
     * While the scheme is unlicensed this must NOT name the scheme, or the
     * markup claims an accreditation the business does not hold.
     */
    private function credential(Course $course): string
    {
        if ($course->scheme && ! $course->scheme->isIndependent()) {
            return $course->scheme->name;
        }

        return $course->certificate ?: 'Academia Certificate of Completion';
    }

    private function verifiedRating(): ?array
    {
        $score = config('academia.reviews.verified_score');
        $count = (int) config('academia.reviews.verified_count');

        if (blank($score) || $count < (int) config('academia.reviews.schema_min_reviews', 20)) {
            return null;
        }

        return [
            '@type'       => 'AggregateRating',
            'ratingValue' => (string) $score,
            'reviewCount' => (string) $count,
            'bestRating'  => '5',
        ];
    }
}
