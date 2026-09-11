<?php

/**
 * Academia domain configuration.
 *
 * Everything here is a *business rule with a public consequence*. Each value
 * is printed somewhere a customer can read it, so changing one is a
 * commercial decision rather than a configuration tweak.
 */
return [

    'legal_entity' => env('ACADEMIA_LEGAL_ENTITY', 'SlimCijfers Analytics B.V.'),
    'trade_name'   => env('ACADEMIA_TRADE_NAME', 'Academia Training Solutions'),
    'email'        => env('ACADEMIA_EMAIL', 'info@academiatraining.eu'),
    'phone'        => env('ACADEMIA_PHONE', '+31 20 000 0000'),

    /*
    |--------------------------------------------------------------------------
    | Group sizes
    |--------------------------------------------------------------------------
    | Published and contractual. These numbers appear on every course page and
    | in the value comparison, so they are a promise, not a default.
    */
    'max_participants' => [
        'classroom' => 14,
        'online'    => 12,
    ],

    /*
    |--------------------------------------------------------------------------
    | Promotions
    |--------------------------------------------------------------------------
    | See spec §9.3. Three rules, enforced in code rather than in a policy
    | document:
    |
    |  1. Every promotion has a reason and an end date (NOT NULL in the
    |     schema, so an evergreen sale cannot be created even by direct SQL).
    |  2. List prices are never inflated to create a discount — Directive
    |     (EU) 2019/2161 requires announcing against the lowest price in the
    |     previous 30 days, which is what `prior_price_cents` records.
    |  3. Stacking stops at the published ceiling below.
    */
    'promotions' => [
        'max_stack_percent' => 30,

        'group_tiers' => [
            ['min_seats' => 3,  'percent' => 15],
            ['min_seats' => 6,  'percent' => 20],
            ['min_seats' => 10, 'percent' => 25],
        ],

        'early_bird' => [
            'percent'   => 10,
            'days'      => 60,
            'code'      => 'EARLY10',
        ],

        // Prices round to the nearest multiple of this, in cents. A price
        // ending in 5 reads as a considered number; .99 reads as a nudge.
        'round_to_cents' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Review verification gate
    |--------------------------------------------------------------------------
    | The site displays NO rating it generated itself. A course or trainer
    | rating returns null below `min_reviews`, and null renders nothing —
    | not "no reviews yet", not zero stars. aggregateRating schema emits only
    | when a verified external score AND a count >= schema_min_reviews exist.
    */
    'reviews' => [
        'min_reviews'        => 5,
        'schema_min_reviews' => 20,
        'verified_score'     => env('ACADEMIA_REVIEW_SCORE'),
        'verified_count'     => env('ACADEMIA_REVIEW_COUNT'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Leads
    |--------------------------------------------------------------------------
    */
    'leads' => [
        'sales_email'      => env('ACADEMIA_SALES_EMAIL', 'sales@academiatraining.eu'),
        'sla_hours'        => (int) env('ACADEMIA_SLA_HOURS', 2),
        'business_hours'   => ['start' => 8, 'end' => 18],
        'business_days'    => [1, 2, 3, 4, 5],
        'consent_version'  => env('ACADEMIA_CONSENT_VERSION', '2026-08-v1'),

        // Minimum seconds between form render and submit. Bots submit
        // instantly; humans do not. Failing this check is silent.
        'min_form_seconds' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Catalogue
    |--------------------------------------------------------------------------
    */
    'catalogue' => [
        'per_page'          => 24,
        'facet_cache_hours' => 1,
        // Above this many facets on one URL, emit noindex,follow (spec §12.4).
        'indexable_facets'  => 1,
    ],

    'schedule' => [
        'default_window_days' => 60,
        'per_page'            => 50,
        // Staleness alerting thresholds (spec §7.6).
        'alerts' => [
            'min_sessions_per_category' => 3,
            'city_lookahead_days'       => 90,
            'min_sessions_beyond_90d'   => 40,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Data retention (spec §22.1)
    |--------------------------------------------------------------------------
    | Booking records: 7 years, required by Dutch tax and administration law.
    | Unconverted enquiries: 24 months. Enforced by a scheduled command, not
    | by a promise in the privacy policy.
    */
    'retention' => [
        'bookings_years'    => 7,
        'dead_leads_months' => 24,
    ],
];
