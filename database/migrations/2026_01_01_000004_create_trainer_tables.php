<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 16)->unique();
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->string('headline', 200)->nullable();
            $table->text('bio_short')->nullable();
            $table->text('bio_full')->nullable();
            $table->unsignedSmallInteger('years_experience')->nullable();
            $table->json('certifications')->nullable();
            $table->json('languages')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();

            // ---------------------------------------------------------------
            // THE ANONYMITY SWITCH — see spec §3.1
            //
            // V2 of this product required that trainer identities are NOT
            // published before a booking is confirmed. The rebuild brief asks
            // for public profiles. Both are supported; this column decides.
            //
            // Default is FALSE deliberately. The safe default is the one that
            // cannot leak: turning a trainer public must be an explicit act.
            // ---------------------------------------------------------------
            $table->boolean('is_public')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('status', 20)->default('active');

            // Internal only — never rendered on a public surface.
            $table->bigInteger('day_rate_cents')->nullable();
            $table->string('contract_type', 40)->nullable();
            $table->text('availability_notes')->nullable();
            $table->date('reference_checked_at')->nullable();
            $table->unsignedTinyInteger('internal_rating')->nullable();
            $table->unsignedSmallInteger('days_delivered')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_public', 'status', 'published_at']);
        });

        Schema::create('course_trainer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_lead')->default(false);
            $table->unique(['course_id', 'trainer_id']);
        });

        Schema::create('course_subcategory_trainer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trainer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_subcategory_id')->constrained()->cascadeOnDelete();
            $table->unique(['trainer_id', 'course_subcategory_id'], 'trainer_expertise_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_subcategory_trainer');
        Schema::dropIfExists('course_trainer');
        Schema::dropIfExists('trainers');
    }
};
