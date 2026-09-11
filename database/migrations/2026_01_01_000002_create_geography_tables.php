<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->char('iso2', 2)->unique();
            $table->string('slug', 100)->unique();
            $table->char('currency', 3)->default('EUR');
            // Cross-border EU B2C training VAT is charged where the training
            // is delivered, so the rate belongs to the country, not the seller.
            $table->decimal('vat_rate', 5, 2)->nullable();
            $table->string('timezone', 40)->default('Europe/Amsterdam');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100);
            // Hand-written, 120-200 words. 26 pages differing only by a city
            // name is spun content and will not rank (spec §8.4). The admin
            // warns while this is empty.
            $table->text('intro')->nullable();
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['country_id', 'slug']);
            $table->index(['is_active', 'name']);
        });

        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('postcode', 20)->nullable();
            $table->text('transport_notes')->nullable();
            $table->json('facilities')->nullable();
            $table->text('accessibility_notes')->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('city_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('countries');
    }
};
