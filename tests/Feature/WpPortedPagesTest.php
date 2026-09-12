<?php

declare(strict_types=1);

use App\Domain\Content\Models\BlogCategory;
use App\Domain\Content\Models\BlogPost;
use App\Domain\Content\Models\CaseStudy;
use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use App\Livewire\Public\CallbackForm;
use App\Livewire\Public\ContactForm;
use App\Livewire\Public\NewsletterForm;
use Illuminate\Support\Str;
use Livewire\Livewire;

it('serves the contact page and stores a contact lead', function () {
    $this->get(route('contact'))->assertOk()->assertSee('Talk to a training advisor', false);

    Livewire::test(ContactForm::class)
        ->set('name', 'Alex Buyer')
        ->set('email', 'alex@example.com')
        ->set('subject', 'Course recommendation')
        ->set('message', 'We need Power BI for finance.')
        ->set('consent', true)
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->call('submit')
        ->assertRedirect(route('thank-you', ['type' => 'contact']));

    $lead = IndividualLead::query()->first();
    expect($lead)->not->toBeNull()
        ->and($lead->source)->toBe(LeadSource::Contact)
        ->and($lead->email)->toBe('alex@example.com');
});

it('stores a callback request lead', function () {
    Livewire::test(CallbackForm::class)
        ->set('name', 'Sam Ops')
        ->set('email', 'sam@example.com')
        ->set('phone', '+31 20 123 4567')
        ->set('preferredWindow', 'Today, afternoon')
        ->set('consent', true)
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->call('submit')
        ->assertRedirect(route('thank-you', ['type' => 'callback']));

    expect(IndividualLead::query()->where('source', LeadSource::CallbackRequest)->count())->toBe(1);
});

it('stores an unconfirmed newsletter lead and confirms it', function () {
    Livewire::test(NewsletterForm::class)
        ->set('email', 'news@example.com')
        ->set('consent', true)
        ->set('renderedAt', now()->subSeconds(10)->timestamp)
        ->call('submit')
        ->assertRedirect(route('thank-you', ['type' => 'newsletter']));

    $lead = IndividualLead::query()->where('source', LeadSource::Newsletter)->first();
    expect($lead)->not->toBeNull()
        ->and($lead->confirmed_at)->toBeNull()
        ->and($lead->confirmation_token)->not->toBeNull();

    $this->get(route('newsletter.confirm', $lead->confirmation_token))
        ->assertRedirect(route('thank-you', ['type' => 'newsletter']));

    expect($lead->fresh()->confirmed_at)->not->toBeNull();
});

it('serves skills credits, pricing, insights and success stories pages', function () {
    $this->get(route('skills-credits'))->assertOk()->assertSee('Skills Credits');
    $this->get(route('why-our-price'))->assertOk();

    $category = BlogCategory::query()->first()
        ?? BlogCategory::query()->create([
            'name' => 'General',
            'slug' => 'general',
        ]);

    $post = BlogPost::query()->create([
        'blog_category_id' => $category->id,
        'title' => 'Ported Insight',
        'slug' => 'ported-insight',
        'excerpt' => 'A short excerpt.',
        'body' => 'Body text for the insight.',
        'reading_minutes' => 3,
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $study = CaseStudy::query()->create([
        'title' => 'Ported Case',
        'slug' => 'ported-case',
        'sector' => 'Finance',
        'summary' => 'A summary of the programme.',
        'background' => 'Background context.',
        'approach_points' => ['Discovery', 'Delivery', 'Follow-up'],
        'status' => 'published',
    ]);

    $this->get(route('insights.index'))->assertOk()->assertSee('Ported Insight');
    $this->get(route('insights.show', $post))->assertOk()->assertSee('Ported Insight');
    $this->get(route('success-stories.index'))->assertOk()->assertSee('Ported Case');
    $this->get(route('success-stories.show', $study))->assertOk()->assertSee('Ported Case');
});
