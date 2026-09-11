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
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 24)->unique();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_mode_id')->constrained()->restrictOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('trainer_id')->nullable()->constrained()->nullOnDelete();

            // Always UTC in the database. The IANA zone lives beside it so the
            // display layer can convert; storage stays absolute.
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('timezone', 40)->default('Europe/Amsterdam');

            $table->unsignedSmallInteger('seat_limit');
            // A counter, not a live COUNT(). Mutated only inside the locked
            // transaction in RegistrationService (spec §7.4).
            $table->unsignedSmallInteger('seats_taken')->default(0);

            $table->bigInteger('price_cents')->nullable();
            $table->char('currency', 3)->default('EUR');
            $table->string('status', 16)->default('scheduled');
            $table->char('language', 2)->default('en');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['starts_at', 'status']);
            $table->index(['city_id', 'starts_at']);
            $table->index(['course_id', 'starts_at']);
            $table->index(['trainer_id', 'starts_at']);
        });

        // Database-level backstop. The service is the only sanctioned write
        // path, but a future code path that bypasses it must still fail
        // rather than oversell a classroom.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('
                ALTER TABLE course_schedules
                ADD CONSTRAINT seats_not_oversold CHECK (seats_taken <= seat_limit)
            ');
            DB::statement('
                ALTER TABLE course_schedules
                ADD CONSTRAINT ends_after_start CHECK (ends_at > starts_at)
            ');
        }

        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            // UUID because this appears in confirmation emails and URLs;
            // a sequential id would leak booking volume to competitors.
            $table->uuid('uuid')->unique();
            $table->foreignId('course_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name', 120);
            $table->string('email', 180);
            $table->string('phone', 32)->nullable();
            $table->string('company', 180)->nullable();
            $table->string('job_title', 120)->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message')->nullable();
            $table->text('dietary_requirements')->nullable();

            $table->unsignedSmallInteger('seats')->default(1);
            $table->string('status', 20)->default('interest');
            $table->bigInteger('price_paid_cents')->nullable();
            $table->string('discount_code', 40)->nullable();
            $table->unsignedTinyInteger('discount_percent')->nullable();

            $table->string('source', 40)->nullable();
            $table->string('utm_source', 80)->nullable();
            $table->string('utm_medium', 80)->nullable();
            $table->string('utm_campaign', 120)->nullable();

            // GDPR Art. 7 proof: consent must be demonstrable, and knowing
            // WHICH wording was agreed to is what makes the record defensible.
            $table->timestamp('consented_at')->nullable();
            $table->string('consent_ip', 45)->nullable();
            $table->string('consent_version', 40)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['course_schedule_id', 'status']);
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
        Schema::dropIfExists('course_schedules');
    }
};
