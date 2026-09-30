<?php

declare(strict_types=1);

use App\Domain\Content\Models\GlossaryTerm;
use App\Domain\Content\Models\Promotion;
use App\Http\Middleware\RedirectLegacyUrls;
use Illuminate\Http\Request;

it('301s legacy wordpress paths and trailing slashes', function () {
    $this->get('/training-catalogue/')
        ->assertStatus(301)
        ->assertRedirect('/courses');

    $this->get('/training-schedule')
        ->assertStatus(301)
        ->assertRedirect('/schedule');

    $this->get('/locations/amsterdam/')
        ->assertStatus(301)
        ->assertRedirect('/classroom-training/netherlands/amsterdam');

    $this->get('/delivery/onsite')
        ->assertStatus(301)
        ->assertRedirect('/corporate-training');
});

it('folds a trailing slash in one hop before the router runs', function () {
    $middleware = new RedirectLegacyUrls;
    $reached = false;

    $legacy = $middleware->handle(
        Request::create('http://academiatraining.eu/training-catalogue/', 'GET'),
        function () use (&$reached) {
            $reached = true;

            return response('nope', 200);
        }
    );

    expect($reached)->toBeFalse()
        ->and($legacy->getStatusCode())->toBe(301)
        ->and($legacy->headers->get('Location'))->toEndWith('/courses');

    $page = $middleware->handle(
        Request::create('http://academiatraining.eu/offers/', 'GET'),
        fn () => response('nope', 200)
    );

    expect($page->getStatusCode())->toBe(301)
        ->and($page->headers->get('Location'))->toEndWith('/offers');
});

it('sends an expertise tag to the glossary when that term exists', function () {
    GlossaryTerm::query()->create([
        'term' => 'GDPR',
        'slug' => 'gdpr',
        'definition' => 'The EU general data protection regulation.',
        'is_active' => true,
    ]);

    $this->get('/expertise/gdpr')
        ->assertStatus(301)
        ->assertRedirect('/glossary/gdpr');

    $this->get('/expertise/power-bi')
        ->assertStatus(301)
        ->assertRedirect('/courses?'.http_build_query(['q' => 'power bi']));
});

it('emits a production canonical and open graph image without indexing staging', function () {
    $this->get('http://academiatraining.eu/about')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="https://academiatraining.eu/about">', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('/images/og-default.png', false)
        ->assertSee('Academia Training EU', false)
        ->assertHeaderMissing('X-Robots-Tag');

    $this->get('http://x.academiatraining.eu/')
        ->assertOk()
        ->assertDontSee('rel="canonical"', false)
        ->assertSee('content="noindex,nofollow"', false)
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

it('hides the placeholder phone and zero registration numbers', function () {
    config([
        'academia.phone' => '+31 20 000 0000',
        'academia.kvk' => '00000000',
        'academia.vat' => 'NL000000000B01',
    ]);

    $this->get('/contact')
        ->assertOk()
        ->assertSee('info@academiatraining.eu', false)
        ->assertDontSee('+31 20 000 0000', false)
        ->assertDontSee('mercury.rakib', false);

    $this->get('/privacy')
        ->assertOk()
        ->assertSee('Company registration details on request', false)
        ->assertDontSee('Have a lawyer read this', false)
        ->assertDontSee('KvK 00000000', false);

    config([
        'academia.phone' => '+31 20 123 4567',
        'academia.kvk' => '12345678',
        'academia.vat' => 'NL123456789B01',
    ]);

    $this->get('/contact')->assertOk()->assertSee('+31 20 123 4567', false);
    $this->get('/privacy')->assertOk()->assertSee('KvK 12345678', false)->assertSee('NL123456789B01', false);
});

it('uses the configured campaign end date on the offers page', function () {
    config(['academia.promotions.featured.ends_at' => '2026-11-15']);

    Promotion::query()->create([
        'name' => 'Autumn Skills Sprint',
        'code' => 'AUTUMN20',
        'percentage' => 20,
        'type' => 'campaign',
        'reason' => 'Filling quiet autumn classrooms.',
        'starts_at' => '2026-09-01 00:00:00',
        'ends_at' => '2026-10-31 23:59:59',
        'blurb' => 'Every technology and finance course.',
        'is_active' => true,
    ]);

    $this->get('/offers')
        ->assertOk()
        ->assertSee('15 November 2026', false)
        ->assertDontSee('31 October 2026', false);
});

it('noindexes an empty insights hub and shows designed pathways on success stories', function () {
    $this->get('http://academiatraining.eu/insights')
        ->assertOk()
        ->assertSee('No articles published yet', false)
        ->assertSee('talk to a training advisor', false)
        ->assertSee('content="noindex,follow"', false);

    $this->get('/success-stories')
        ->assertOk()
        ->assertSee('Financial analyst', false)
        ->assertSee('These describe programme design, not individual participants.', false)
        ->assertSee('once a client has signed them off', false);
});
