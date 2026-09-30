<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Services\CourseOutlinePdf;
use Symfony\Component\HttpFoundation\Response;

/**
 * Signed download for an outline that was requested by email.
 *
 * The public course page does not link here. The URL is minted when someone
 * submits the outline form, and it expires.
 */
class CourseOutlineController extends Controller
{
    public function download(Course $course, CourseOutlinePdf $outlines): Response
    {
        abort_unless($course->isPublished(), 404);

        $pdf = $outlines->render($course);
        $filename = $outlines->filename($course);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
