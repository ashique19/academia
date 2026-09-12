<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Catalogue\Services\PromotionService;
use App\Domain\Content\Models\BlogPost;
use App\Domain\Content\Models\CaseStudy;
use App\Domain\Content\Models\Faq;
use App\Domain\Content\Models\GlossaryTerm;
use App\Domain\Leads\Models\IndividualLead;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function skillsCredits(): View
    {
        return view('pages.skills-credits');
    }

    public function whyOurPrice(): View
    {
        return view('pages.why-our-price');
    }

    public function insights(): View
    {
        return view('pages.insights.index', [
            'posts' => BlogPost::query()
                ->published()
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->paginate(9),
        ]);
    }

    public function insight(BlogPost $post): View
    {
        abort_unless($post->status === 'published'
            && $post->published_at !== null
            && $post->published_at->lte(now()), 404);

        $post->load(['category', 'author', 'seo']);

        return view('pages.insights.show', compact('post'));
    }

    public function successStories(): View
    {
        return view('pages.success-stories.index', [
            'studies' => CaseStudy::query()->published()->orderBy('title')->get(),
        ]);
    }

    public function successStory(CaseStudy $study): View
    {
        abort_unless($study->status === 'published', 404);

        return view('pages.success-stories.show', compact('study'));
    }

    /**
     * Double opt-in confirmation for newsletter signups.
     */
    public function confirmNewsletter(string $token): RedirectResponse
    {
        $lead = IndividualLead::query()
            ->where('confirmation_token', $token)
            ->where('source', 'newsletter')
            ->firstOrFail();

        if ($lead->confirmed_at === null) {
            $lead->forceFill([
                'confirmed_at' => now(),
                'confirmation_token' => null,
            ])->save();
        }

        return redirect()->route('thank-you', ['type' => 'newsletter']);
    }

    public function corporate(): View
    {
        return view('pages.corporate', [
            'faqs' => Faq::active()->global()->inGroup('corporate')->orderBy('sort_order')->get(),
        ]);
    }

    public function online(): View
    {
        return view('pages.online', [
            'courses' => Course::published()->withDeliveryMode(['online'])
                ->withCardRelations()->orderByDesc('booking_count')->take(8)->get(),
        ]);
    }

    public function offers(PromotionService $promotions): View
    {
        return view('pages.offers', [
            'promotion' => $promotions->active(),
            'groupTiers' => $promotions->groupTiers(),
            'maxStack' => $promotions->maxStackPercent(),
            'earlyBird' => config('academia.promotions.early_bird'),
        ]);
    }

    public function faq(): View
    {
        return view('pages.faq', [
            'faqs' => Faq::active()->global()->orderBy('group')->orderBy('sort_order')->get()
                ->groupBy('group'),
        ]);
    }

    public function glossary(): View
    {
        return view('pages.glossary', [
            'terms' => GlossaryTerm::active()->orderBy('term')->get(),
        ]);
    }

    public function glossaryTerm(GlossaryTerm $term): View
    {
        abort_unless($term->is_active, 404);

        return view('pages.glossary-term', [
            'term' => $term->load('subcategory'),
            'courses' => $term->course_subcategory_id
                ? Course::published()->where('course_subcategory_id', $term->course_subcategory_id)
                    ->withCardRelations()->take(3)->get()
                : collect(),
        ]);
    }

    /**
     * Faculty index.
     *
     * Uses the ->public() scope exclusively. If trainer anonymity is the
     * chosen position (spec §3.1), no trainer is public, the collection is
     * empty and this route 404s — with no template changes required.
     */
    public function trainers(): View
    {
        $trainers = Trainer::public()->with('city')->orderBy('name')->get();

        abort_if($trainers->isEmpty(), 404);

        return view('pages.trainers', compact('trainers'));
    }

    public function trainer(Trainer $trainer): View
    {
        abort_unless($trainer->isPubliclyVisible(), 404);

        return view('pages.trainer', [
            'trainer' => $trainer->load(['city', 'expertise', 'courses' => fn ($q) => $q->published()->take(6)]),
        ]);
    }

    public function thankYou(string $type): View
    {
        abort_unless(in_array($type, [
            'corporate', 'registration', 'interest', 'brochure', 'callback', 'newsletter', 'contact',
        ], true), 404);

        return view('pages.thank-you', compact('type'));
    }

    public function legal(string $document): View
    {
        abort_unless(in_array($document, [
            'privacy', 'terms', 'cancellation-policy', 'cookie-settings',
        ], true), 404);

        return view('pages.legal.'.$document);
    }
}
