<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Content\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Seeds ILLUSTRATIVE testimonials only.
 *
 * Every row here has is_illustrative = true and is_verified = false. They
 * describe the outcome a course is DESIGNED to produce — a claim about
 * programme design, which Academia can substantiate — and they render with a
 * visible "Illustrative" marker.
 *
 * There are deliberately no invented named customers. Anonymising a fabricated
 * quote does not make it true; it makes it harder to disprove. Real verified
 * testimonials arrive through the post-course feedback flow and are marked
 * verified by an admin with a consent reference on file.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $illustrative = [
            [
                'role'   => 'Data & BI',
                'sector' => 'Classroom',
                'quote'  => 'Built something with our own company data on day one, and it was still in '
                          . 'use two weeks later.',
            ],
            [
                'role'   => 'Leadership',
                'sector' => 'Multi-country in-company',
                'quote'  => 'Case studies adapted per market, and one consistent report on capability '
                          . 'gaps across every site.',
            ],
            [
                'role'   => 'Systems training',
                'sector' => 'Live online',
                'quote'  => 'Recordings and a real lab environment, so practising around a full-time '
                          . 'job was actually possible.',
            ],
        ];

        foreach ($illustrative as $order => $row) {
            Testimonial::updateOrCreate(
                ['quote' => $row['quote']],
                [
                    'author_role'     => $row['role'],
                    'author_sector'   => $row['sector'],
                    'is_illustrative' => true,
                    'is_verified'     => false,
                    'status'          => 'published',
                    'sort_order'      => $order,
                ]
            );
        }
    }
}
