<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->string('summary', 320)->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 40)->nullable();
            $table->string('color_token', 20)->nullable();
            // Manual ordering. Never sort categories alphabetically — the
            // order is a merchandising decision.
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('course_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_category_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('slug', 120);
            $table->string('summary', 320)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Scoped, not global: "Reporting" may exist under two parents.
            $table->unique(['course_category_id', 'slug']);
        });

        Schema::create('delivery_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug', 60)->unique();
            $table->string('icon', 40)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('certification_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            $table->string('owner', 160);
            // 'independent' | 'accredited' — the single value that drives the
            // course title suffix, the trademark notice and the schema output.
            $table->string('status', 20)->default('independent');
            $table->string('match_needle', 120);
            $table->string('exam_questions', 60)->nullable();
            $table->string('exam_format', 120)->nullable();
            $table->string('exam_pass_mark', 40)->nullable();
            $table->string('exam_duration', 60)->nullable();
            $table->string('exam_book', 60)->nullable();
            $table->text('pathway')->nullable();
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_subcategory_id')->constrained()->restrictOnDelete();
            $table->foreignId('certification_scheme_id')->nullable()
                ->constrained()->nullOnDelete();
            $table->string('code', 16)->unique();
            $table->string('title', 200);
            // Immutable after publish. A change writes a 301 redirect row and
            // logs it — never a silent rewrite (spec §12.3).
            $table->string('slug', 220)->unique();
            $table->string('summary', 320);
            $table->text('description');
            $table->json('learning_objectives');
            $table->text('target_audience')->nullable();
            $table->text('prerequisites')->nullable();
            $table->json('includes')->nullable();
            $table->decimal('duration_days', 3, 1);
            $table->unsignedSmallInteger('duration_hours')->nullable();
            $table->string('level', 20);
            $table->unsignedSmallInteger('max_participants')->default(14);

            // Money is always an integer. Never DECIMAL, never FLOAT.
            $table->bigInteger('price_cents')->nullable();
            // EU Omnibus (Directive 2019/2161) reference price: the lowest
            // price applied in the 30 days before a reduction. Recorded
            // before a campaign starts so the crossed-out figure is
            // defensible if a consumer authority asks.
            $table->bigInteger('prior_price_cents')->nullable();
            $table->bigInteger('self_paced_price_cents')->nullable();
            $table->bigInteger('day_rate_cents')->nullable();
            $table->char('currency', 3)->default('EUR');

            $table->string('certificate', 160)->nullable();
            $table->text('certification_note')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();

            // Denormalised: "soonest upcoming open session" is a per-row
            // aggregate that eager loading cannot solve. Maintained by
            // CourseScheduleObserver and recomputed nightly as a safety net.
            $table->timestamp('next_session_at')->nullable();

            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('booking_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index('course_subcategory_id');
            $table->index('level');
            $table->index('next_session_at');
            $table->index('is_featured');
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('body')->nullable();
            $table->json('bullets')->nullable();
            $table->decimal('duration_hours', 4, 1)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('course_delivery_mode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_mode_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('price_cents')->nullable();
            $table->unique(['course_id', 'delivery_mode_id']);
        });

        Schema::create('related_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unique(['course_id', 'related_course_id']);
        });

        // --- Postgres-only refinements -------------------------------------
        // Partial indexes and full-text search. Skipped on SQLite/MySQL,
        // where the plain indexes above are sufficient at this scale.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('
                CREATE INDEX courses_published_idx ON courses (status, published_at DESC)
                WHERE deleted_at IS NULL
            ');
            DB::statement("
                CREATE INDEX courses_next_session_idx ON courses (next_session_at)
                WHERE status = 'published' AND deleted_at IS NULL
            ");
            DB::statement("
                ALTER TABLE courses ADD COLUMN search_vector tsvector
                GENERATED ALWAYS AS (
                    setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                    setweight(to_tsvector('english', coalesce(summary, '')), 'B')
                ) STORED
            ");
            DB::statement('CREATE INDEX courses_search_idx ON courses USING GIN (search_vector)');
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE courses ADD FULLTEXT courses_search_idx (title, summary)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('related_course');
        Schema::dropIfExists('course_delivery_mode');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('certification_schemes');
        Schema::dropIfExists('delivery_modes');
        Schema::dropIfExists('course_subcategories');
        Schema::dropIfExists('course_categories');
    }
};
