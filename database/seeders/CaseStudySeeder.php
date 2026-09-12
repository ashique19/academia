<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\CaseStudy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Published case studies without named clients.
 *
 * Client name/quote slots stay empty until `client_approved` is set — matching
 * the CaseStudy model contract so the admin panel has realistic rows without
 * inventing testimonials.
 */
class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $studies = [
            [
                'title' => 'Finance academy for a pan-European shared-service centre',
                'sector' => 'Shared services',
                'summary' => 'A twelve-week capability programme covering IFRS refreshers, Excel-to-Power-BI pathways and month-end controls for 86 analysts across four hubs.',
                'background' => 'The client had grown through acquisition. Each hub used different month-end checklists and BI tools, so consolidation took longer than the board would accept.',
                'approach_points' => [
                    'Diagnostic interviews with team leads in each hub',
                    'One master curriculum with local ledger examples',
                    'Classroom cohorts of max 14, followed by remote clinics',
                    'Capability scorecard shared with the finance leadership team',
                ],
            ],
            [
                'title' => 'Leadership essentials for first-time managers in logistics',
                'sector' => 'Logistics',
                'summary' => 'Three-day essentials plus a 60-day coaching cadence for newly promoted supervisors across warehouse and planning roles.',
                'background' => 'Promotion from within was creating strong operators who had never run a one-to-one. Attrition in the first year after promotion was the commercial problem.',
                'approach_points' => [
                    'Cohort of 12 supervisors per wave',
                    'Role-play built from real shift-handover scenarios',
                    'Manager toolkit issued with joining instructions',
                    'Follow-up clinics at day 30 and day 60',
                ],
            ],
            [
                'title' => 'SAP end-user uplift before a S/4HANA cutover',
                'sector' => 'Manufacturing',
                'summary' => 'Role-based classroom and live-online training for purchasing, warehouse and finance users in the eight weeks before go-live.',
                'background' => 'The project team owned the system build. Line managers owned adoption. Without a shared training plan, floor questions would land on a stretched hypercare team.',
                'approach_points' => [
                    'Training needs analysis mapped to Fiori roles',
                    'Sandbox exercises on the client’s own org structure',
                    'Train-the-trainer for internal champions',
                    'Floorwalk support during the first two weeks of hypercare',
                ],
            ],
        ];

        foreach ($studies as $study) {
            CaseStudy::updateOrCreate(
                ['slug' => Str::slug($study['title'])],
                [
                    'title' => $study['title'],
                    'sector' => $study['sector'],
                    'summary' => $study['summary'],
                    'background' => $study['background'],
                    'approach_points' => $study['approach_points'],
                    'client_name' => null,
                    'client_quote' => null,
                    'client_quote_attribution' => null,
                    'client_approved' => false,
                    'status' => 'published',
                ]
            );
        }
    }
}
