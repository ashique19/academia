<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Catalogue\Services\PromotionService;
use App\Domain\Content\Models\Faq;
use App\Domain\Content\Models\GlossaryTerm;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
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
            'corporate', 'registration', 'interest', 'brochure', 'callback', 'newsletter',
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
