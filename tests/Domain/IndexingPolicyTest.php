<?php

declare(strict_types=1);

use App\Domain\Content\Services\IndexingPolicy;

it('keeps the production host indexable even when the app env is staging', function () {
    $this->app['env'] = 'staging';
    request()->headers->set('HOST', 'academiatraining.eu');

    $policy = app(IndexingPolicy::class);

    expect($policy->allowsIndexing('academiatraining.eu'))->toBeTrue()
        ->and($policy->allowsIndexing('WWW.AcademiaTraining.eu'))->toBeTrue()
        ->and($policy->directive(null))->toBe('index,follow')
        ->and($policy->directive('noindex,follow'))->toBe('noindex,follow');
});

it('noindexes the staging host even when the app env is production', function () {
    $this->app['env'] = 'production';
    request()->headers->set('HOST', 'x.academiatraining.eu');

    $policy = app(IndexingPolicy::class);

    expect($policy->allowsIndexing('x.academiatraining.eu'))->toBeFalse()
        ->and($policy->allowsIndexing('x.preview.example'))->toBeFalse()
        ->and($policy->directive('index,follow'))->toBe('noindex,nofollow');
});

it('indexes unknown hosts only in production', function () {
    $policy = app(IndexingPolicy::class);

    $this->app['env'] = 'local';
    expect($policy->allowsIndexing('localhost'))->toBeFalse();

    $this->app['env'] = 'production';
    expect($policy->allowsIndexing('localhost'))->toBeTrue()
        ->and($policy->directive(null))->toBe('index,follow')
        ->and($policy->directive('noindex,follow'))->toBe('noindex,follow');
});
