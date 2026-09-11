<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('code', 40)->unique();
            $table->unsignedTinyInteger('percentage');
            $table->string('type', 20)->default('campaign');

            // NOT NULL, deliberately. Rule 1 of the promotion policy is
            // "every offer has a named reason and an end date" — enforcing it
            // in the schema means an evergreen sale cannot be created even by
            // someone writing SQL directly.
            $table->text('reason');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            $table->string('blurb', 320)->nullable();
            $table->json('applies_to_category_slugs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->string('summary', 320)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('excerpt', 400)->nullable();
            $table->longText('body')->nullable();
            $table->unsignedSmallInteger('reading_minutes')->nullable();
            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author_name', 120)->nullable();
            $table->string('author_role', 120)->nullable();
            $table->string('author_sector', 120)->nullable();
            $table->string('organisation', 180)->nullable();
            $table->text('quote');
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('delivery_mode', 40)->nullable();
            $table->unsignedTinyInteger('rating')->nullable();

            // ---------------------------------------------------------------
            // THE VERIFICATION GATE — see spec §3.1
            //
            // A testimonial may be published only if it is verified (a real,
            // permissioned quote with a consent reference on file) OR flagged
            // illustrative (describing a designed outcome, and rendered with
            // a visible "Illustrative" marker).
            //
            // Enforced by TestimonialObserver, not by a policy document,
            // because policies are forgotten under publishing pressure.
            // ---------------------------------------------------------------
            $table->boolean('is_verified')->default(false);
            $table->string('consent_reference', 120)->nullable();
            $table->boolean('is_illustrative')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->string('status', 16)->default('draft');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['status', 'is_verified']);
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('faqable');
            $table->string('question', 320);
            $table->text('answer');
            $table->string('group', 60)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('glossary_terms', function (Blueprint $table) {
            $table->id();
            $table->string('term', 160);
            $table->string('slug', 180)->unique();
            $table->text('definition');
            $table->text('body')->nullable();
            $table->foreignId('course_subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('sector', 120)->nullable();
            $table->string('summary', 400)->nullable();
            $table->text('background')->nullable();
            $table->json('approach_points')->nullable();
            // Deliberately nullable and deliberately empty at launch: a case
            // study publishes a client name and a number only once the client
            // has signed both off.
            $table->string('client_name', 180)->nullable();
            $table->text('client_quote')->nullable();
            $table->string('client_quote_attribution', 180)->nullable();
            $table->boolean('client_approved')->default(false);
            $table->string('status', 16)->default('draft');
            $table->timestamps();
        });

        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('title', 200)->nullable();
            $table->string('description', 400)->nullable();
            $table->string('keywords', 320)->nullable();
            $table->string('og_title', 200)->nullable();
            $table->string('og_description', 400)->nullable();
            $table->string('og_image_path')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots', 60)->default('index,follow');
            $table->json('schema_overrides')->nullable();
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id'], 'seo_metadata_unique');
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path', 320)->unique();
            $table->string('to_path', 320);
            $table->unsignedSmallInteger('status_code')->default(301);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 120)->unique();
            $table->json('value')->nullable();
            $table->string('group', 60)->default('general');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'settings', 'redirects', 'seo_metadata', 'case_studies', 'glossary_terms',
            'faqs', 'testimonials', 'blog_posts', 'blog_categories', 'promotions',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
