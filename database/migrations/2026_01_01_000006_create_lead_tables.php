<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corporate_inquiries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->string('company_name', 180);
            $table->string('sector', 80)->nullable();
            $table->string('company_size', 40)->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();

            $table->string('contact_name', 120);
            $table->string('job_title', 120)->nullable();
            $table->string('email', 180);
            $table->string('phone', 32)->nullable();

            $table->unsignedSmallInteger('participants');
            $table->foreignId('delivery_mode_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('topic', 240)->nullable();
            $table->date('preferred_start_date')->nullable();
            $table->string('preferred_window', 60)->nullable();
            // A select, not free text. Ranges get answered more honestly than
            // a box, and "not yet defined" is a real qualification signal
            // rather than a blank.
            $table->string('budget_range', 40)->nullable();
            $table->text('message')->nullable();

            $table->string('status', 24)->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            // Without these, conversion reporting is a count rather than a
            // number, and a count cannot justify marketing spend.
            $table->bigInteger('estimated_value_cents')->nullable();
            $table->bigInteger('won_value_cents')->nullable();
            $table->string('lost_reason', 240)->nullable();

            // The SLA clock. A lead form without a measured first-response
            // time is a form that generates leads nobody answers.
            $table->timestamp('first_response_due_at')->nullable();
            $table->timestamp('first_responded_at')->nullable();

            $table->string('source', 40)->nullable();
            $table->string('utm_source', 80)->nullable();
            $table->string('utm_medium', 80)->nullable();
            $table->string('utm_campaign', 120)->nullable();

            $table->timestamp('consented_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('consent_version', 40)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('assigned_to');
            $table->index('first_response_due_at');
            $table->index('email');
        });

        // Append-only history. The current status is never overwritten
        // without recording who moved it, when, and why.
        Schema::create('lead_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_inquiry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status', 24)->nullable();
            $table->string('to_status', 24);
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['corporate_inquiry_id', 'created_at']);
        });

        Schema::create('individual_leads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 120)->nullable();
            $table->string('email', 180);
            $table->string('phone', 32)->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_schedule_id')->nullable()->constrained()->nullOnDelete();
            $table->date('preferred_date')->nullable();
            $table->text('message')->nullable();

            // Discriminator. One table with a source column rather than five
            // tables, so "leads by source" is a GROUP BY not a UNION.
            $table->string('source', 40);
            $table->string('status', 24)->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->string('utm_source', 80)->nullable();
            $table->string('utm_medium', 80)->nullable();
            $table->string('utm_campaign', 120)->nullable();

            $table->timestamp('consented_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('consent_version', 40)->nullable();
            $table->timestamp('confirmed_at')->nullable();   // newsletter double opt-in
            $table->string('confirmation_token', 64)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['source', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('email');
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->morphs('notable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('individual_leads');
        Schema::dropIfExists('lead_status_changes');
        Schema::dropIfExists('corporate_inquiries');
    }
};
