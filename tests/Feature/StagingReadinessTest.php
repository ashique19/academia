<?php

declare(strict_types=1);

use App\Domain\Catalogue\Models\Course;
use App\Domain\Content\Models\GlossaryTerm;

it('renders the offers page', function () {
    $this->get('/offers')
        ->assertOk()
        ->assertSee('Every discount we run', false)
        ->assertSee('Academia Skills Credits', false)
        ->assertSee('Talk to an advisor', false)
        ->assertSee(route('skills-credits'), false);
});

it('noindexes non-production hosts in the page and in robots.txt', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,nofollow">', false)
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $robots = $this->get('/robots.txt');

    $robots->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    expect($robots->getContent())
        ->toContain("Disallow: /\n")
        ->toContain('Sitemap: '.url('/sitemap.xml'))
        ->not->toContain('Allow: /');

    $this->get('http://x.academiatraining.eu/courses')
        ->assertOk()
        ->assertSee('content="noindex,nofollow"', false)
        ->assertDontSee('content="index,follow"', false)
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

it('keeps the production host indexable and points robots.txt at that host', function () {
    $this->app['env'] = 'staging';

    $home = $this->get('http://academiatraining.eu/');

    $home->assertOk()
        ->assertSee('<meta name="robots" content="index,follow">', false)
        ->assertHeaderMissing('X-Robots-Tag');

    $this->get('http://www.academiatraining.eu/thank-you/contact')
        ->assertOk()
        ->assertSee('<meta name="robots" content="noindex,follow">', false)
        ->assertHeaderMissing('X-Robots-Tag');

    $robots = $this->get('http://academiatraining.eu/robots.txt');

    $robots->assertOk();

    expect($robots->getContent())
        ->toContain("Disallow: /thank-you/\n")
        ->toContain("Disallow: /admin/\n")
        ->toContain("Allow: /\n")
        ->toContain('Sitemap: http://academiatraining.eu/sitemap.xml')
        ->not->toContain("Disallow: /\n");
});

it('serves a sitemap of public pages for the current host', function () {
    $published = Course::factory()->create([
        'title' => 'Leadership Essentials',
        'slug' => 'leadership-essentials',
    ]);

    Course::factory()->draft()->create([
        'title' => 'Hidden Draft',
        'slug' => 'hidden-draft',
    ]);

    GlossaryTerm::query()->create([
        'term' => 'Lead time',
        'slug' => 'lead-time',
        'definition' => 'The time between order and delivery.',
        'is_active' => true,
    ]);

    $sitemap = $this->get('http://x.academiatraining.eu/sitemap.xml');

    $sitemap->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    expect($sitemap->getContent())
        ->toContain('<loc>http://x.academiatraining.eu/</loc>')
        ->toContain('<loc>http://x.academiatraining.eu/offers</loc>')
        ->toContain('<loc>http://x.academiatraining.eu/courses</loc>')
        ->toContain('<loc>http://x.academiatraining.eu/courses/'.$published->slug.'</loc>')
        ->toContain('<loc>http://x.academiatraining.eu/glossary/lead-time</loc>')
        ->toContain('<loc>http://x.academiatraining.eu/privacy</loc>')
        ->not->toContain('hidden-draft')
        ->not->toContain('thank-you')
        ->not->toContain('academiatraining.eu/sitemap');

    $this->get('http://x.academiatraining.eu/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: http://x.academiatraining.eu/sitemap.xml', false);
});
