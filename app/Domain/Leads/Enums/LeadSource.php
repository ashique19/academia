<?php

declare(strict_types=1);

namespace App\Domain\Leads\Enums;

/**
 * Discriminator on individual_leads.
 *
 * One table with a source column, rather than five tables, so that
 * "leads by source" is a GROUP BY instead of a five-way UNION.
 */
enum LeadSource: string
{
    case CourseInterest = 'course_interest';
    case CityPage = 'city_page';
    case BrochureDownload = 'brochure_download';
    case CallbackRequest = 'callback_request';
    case Newsletter = 'newsletter';
    case OnlineTraining = 'online_training';
    case Contact = 'contact';

    public function label(): string
    {
        return ucwords(str_replace('_', ' ', $this->value));
    }
}
