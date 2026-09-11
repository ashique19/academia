<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $general = [
            ['What language is the training delivered in?',
                'English, at every location and in every delivery mode. Materials, case studies and '
                .'regulatory examples are localised to the market you work in, but the teaching language '
                .'is English throughout — which is what makes one cohort work across several countries.'],

            ['How large are the groups?',
                'Maximum 14 participants in a classroom and 12 online. Both figures are published and '
                .'contractual, not a target.'],

            ['Who will teach my course?',
                'A subject-matter expert with 10+ years in the field who is still practising, '
                .'reference-checked and matched to your cohort. We confirm the named expert with your '
                .'joining instructions.'],

            ['What happens if I need to cancel?',
                'More than 14 days before the start date: a full refund or a free transfer. Between 14 and '
                .'7 days: a free transfer, or a refund less 25%. Inside 7 days the fee stands, but you may '
                .'send a colleague in your place at any time up to the start, including on the day.'],

            ['What happens if you cancel?',
                'A full refund or a free transfer, and we reimburse non-refundable travel and accommodation '
                .'booked for that date on production of the receipt. We do not reserve a right to substitute '
                .'a virtual course for a classroom one you paid for.'],

            ['Do you offer discounts?',
                'Yes, and we publish every one of them with its end date on the offers page. Group rates '
                .'apply automatically from three people. Combined savings are capped at a published '
                .'ceiling, and we never raise a list price to make a discount look larger.'],

            ['Is the exam included in certification courses?',
                'No. Where a course prepares you for a third-party certification we say so explicitly, name '
                .'the scheme owner, and tell you exactly how to book the exam. Our fee does not include it.'],
        ];

        foreach ($general as $order => [$question, $answer]) {
            Faq::updateOrCreate(
                ['question' => $question, 'faqable_type' => null, 'faqable_id' => null],
                ['answer' => $answer, 'group' => 'general', 'sort_order' => $order, 'is_active' => true]
            );
        }

        $corporate = [
            ['How quickly will I get a proposal?',
                'Within two working days of the enquiry, at a fixed price, with the curriculum outline '
                .'included. A training advisor calls you first if anything needs clarifying.'],

            ['Can you deliver across several countries?',
                'Yes. We regularly run the same programme across multiple sites in different countries, '
                .'with examples localised to each market and one central capability report covering all sites.'],

            ['What is the minimum group size for in-company delivery?',
                'In-company delivery is cost-effective from six participants. Below that, public course '
                .'seats with a group discount are usually cheaper, and we will tell you so.'],

            ['Where is our data processed?',
                'Inside the EEA, under a signed data processing agreement. We can provide the DPA and our '
                .'sub-processor list before contracting.'],
        ];

        foreach ($corporate as $order => [$question, $answer]) {
            Faq::updateOrCreate(
                ['question' => $question, 'faqable_type' => null, 'faqable_id' => null],
                ['answer' => $answer, 'group' => 'corporate', 'sort_order' => $order, 'is_active' => true]
            );
        }
    }
}
