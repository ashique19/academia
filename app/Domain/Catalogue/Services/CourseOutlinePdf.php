<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Services;

use App\Domain\Catalogue\Models\Course;
use App\Support\PublicSite;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Two-page-style course outline built from the catalogue already in the database.
 *
 * PDF rendering uses dompdf/dompdf (Dompdf\Options), a production Composer
 * require. A missing class means vendor was not updated: composer install --no-dev.
 *
 * There is no imported WordPress PDF. Syllabus, objectives, prerequisites,
 * dates and the current price are rendered here so the emailed file matches
 * the course page.
 */
class CourseOutlinePdf
{
    public function __construct(private PromotionService $promotions) {}

    public function render(Course $course): string
    {
        $options = new Options;
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($this->html($course), 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->render();

        return $dompdf->output();
    }

    public function html(Course $course): string
    {
        $course->loadMissing([
            'subcategory',
            'modules',
            'schedules' => fn ($query) => $query->upcoming()->publiclyVisible()
                ->with(['city', 'deliveryMode'])
                ->orderBy('starts_at')
                ->take(8),
        ]);

        return view('pdf.course-outline', [
            'course' => $course,
            'priceLabel' => $this->priceLabel($course),
            'brand' => PublicSite::name(),
        ])->render();
    }

    public function filename(Course $course): string
    {
        return $course->slug.'-outline.pdf';
    }

    private function priceLabel(Course $course): string
    {
        $price = $this->promotions->priceFor($course, 1, $course->next_session?->starts_at);

        if ($price->requiresQuote() || $price->finalPriceCents === null) {
            return 'Price on request';
        }

        $amount = '€'.number_format($price->finalPriceCents / 100, 0, ',', '.');

        if ($price->hasDiscount()) {
            $amount .= ' ('.$price->discountPercent.'% off the list price)';
        }

        return $amount.' per person, excluding VAT';
    }
}
