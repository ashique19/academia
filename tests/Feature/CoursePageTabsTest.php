<?php

declare(strict_types=1);

use App\Domain\Catalogue\Enums\SchemeStatus;
use App\Domain\Catalogue\Models\CertificationScheme;
use App\Domain\Catalogue\Models\Course;
use App\Domain\Content\Models\Faq;
use App\Domain\Content\Models\Testimonial;

it('renders course tabs with includes, exam details and FAQs', function () {
    $scheme = CertificationScheme::query()->create([
        'name' => 'PRINCE2®',
        'slug' => 'prince2-test',
        'owner' => 'AXELOS Limited',
        'status' => SchemeStatus::Independent,
        'match_needle' => 'prince2-test',
        'exam_questions' => '60',
        'exam_format' => 'Multiple choice',
        'exam_pass_mark' => '55%',
        'exam_duration' => '60 minutes',
        'exam_book' => 'Closed book',
        'pathway' => 'Foundation|Practitioner',
    ]);

    $course = Course::factory()->create([
        'certification_scheme_id' => $scheme->id,
        'title' => 'PRINCE2 Foundation Prep',
        'slug' => 'prince2-foundation-prep-tabs',
        'includes' => [
            'Full course materials and slide deck',
            'Digital toolkit: templates and checklists',
        ],
        'learning_objectives' => [
            'Explain the PRINCE2 principles',
            'Apply the themes to a simple project',
        ],
        'target_audience' => 'New project coordinators|Team leads new to governance',
        'prerequisites' => 'None required.',
    ]);

    $course->faqs()->create([
        'question' => 'Is the official exam included?',
        'answer' => 'No. You book the exam separately with the scheme owner.',
        'group' => 'course',
        'sort_order' => 0,
        'is_active' => true,
    ]);

    Testimonial::create([
        'course_id' => $course->id,
        'quote' => 'The outcome this prep course is designed to produce.',
        'author_role' => 'Project coordinator',
        'is_illustrative' => true,
        'status' => 'published',
        'sort_order' => 0,
    ]);

    $this->get(route('courses.show', $course))
        ->assertOk()
        ->assertSee('Overview', false)
        ->assertSee('Objectives', false)
        ->assertSee('The exam', false)
        ->assertSee('FAQ', false)
        ->assertSee('Reviews', false)
        ->assertSee('Full course materials and slide deck', false)
        ->assertSee('Multiple choice', false)
        ->assertSee('Is the official exam included?', false)
        ->assertSee('Illustrative', false)
        ->assertSee('Independent PRINCE2® exam preparation', false);
});

it('falls back to global FAQs when the course has none', function () {
    Faq::query()->create([
        'question' => 'How large are the groups?',
        'answer' => 'Maximum 14 in a classroom.',
        'group' => 'general',
        'sort_order' => 0,
        'is_active' => true,
        'faqable_type' => null,
        'faqable_id' => null,
    ]);

    $course = Course::factory()->create([
        'slug' => 'excel-essentials-tabs',
        'includes' => ['Workbook and templates'],
    ]);

    $this->get(route('courses.show', $course))
        ->assertOk()
        ->assertSee('FAQ', false)
        ->assertSee('How large are the groups?', false)
        ->assertSee('Common questions about booking and delivery', false)
        ->assertDontSee('The exam', false)
        ->assertDontSee('Reviews', false);
});
