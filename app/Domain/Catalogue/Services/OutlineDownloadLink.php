<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Services;

use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Leads\Models\IndividualLead;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Signed outline URL for the thank-you page.
 *
 * The lead uuid travels on the redirect itself (?lead=). Session is only a
 * backup: a flash is dropped when the cookie does not survive the Livewire
 * redirect (SESSION_DOMAIN, secure cookie, or a host that does not match
 * APP_URL). The page mints a fresh signature from the brochure lead so the
 * button does not depend on that flash.
 */
class OutlineDownloadLink
{
    public const DAYS = 14;

    public function resolve(?string $leadUuid): ?string
    {
        $uuid = $this->uuid($leadUuid) ?? $this->uuid(session('outline_lead_uuid'));

        if ($uuid !== null) {
            $lead = IndividualLead::query()
                ->with('course')
                ->where('uuid', $uuid)
                ->where('source', LeadSource::BrochureDownload)
                ->where('created_at', '>=', now()->subDays(self::DAYS))
                ->first();

            if ($url = $this->urlForLead($lead)) {
                return $url;
            }
        }

        $flashed = session('outline_download_url');

        return is_string($flashed) && $flashed !== '' ? $flashed : null;
    }

    public function urlForLead(?IndividualLead $lead): ?string
    {
        $course = $lead?->course;

        if ($lead === null || $course === null || ! $course->isPublished()) {
            return null;
        }

        return URL::temporarySignedRoute(
            'courses.outline',
            now()->addDays(self::DAYS),
            ['course' => $course],
        );
    }

    private function uuid(mixed $value): ?string
    {
        return is_string($value) && Str::isUuid($value) ? $value : null;
    }
}
