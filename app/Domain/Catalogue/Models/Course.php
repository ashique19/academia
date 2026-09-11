<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Models;

use App\Domain\Catalogue\Enums\CourseLevel;
use App\Domain\Catalogue\Enums\CourseStatus;
use App\Domain\Content\Models\Faq;
use App\Domain\Content\Models\SeoMetadata;
use App\Domain\Content\Models\Testimonial;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** Factories live in Database\Factories, outside this model's namespace. */
    protected static function newFactory(): Factory
    {
        return CourseFactory::new();
    }

    protected $fillable = [
        'course_subcategory_id', 'certification_scheme_id', 'code', 'title', 'slug',
        'summary', 'description', 'learning_objectives', 'target_audience',
        'prerequisites', 'includes', 'duration_days', 'duration_hours', 'level',
        'max_participants', 'price_cents', 'prior_price_cents',
        'self_paced_price_cents', 'day_rate_cents', 'currency', 'certificate',
        'certification_note', 'is_featured', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'learning_objectives' => 'array',
            'includes' => 'array',
            'duration_days' => 'decimal:1',
            'level' => CourseLevel::class,
            'status' => CourseStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'next_session_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /* ------------------------------------------------------------ relations */

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(CourseSubcategory::class, 'course_subcategory_id');
    }

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(CertificationScheme::class, 'certification_scheme_id');
    }

    public function deliveryModes(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryMode::class)->withPivot('price_cents');
    }

    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(Trainer::class)->withPivot('is_lead');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('sort_order');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'faqable')->orderBy('sort_order');
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /* --------------------------------------------------------------- scopes */

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CourseStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Everything a course card touches, in one go.
     *
     * A catalogue page renders 24 cards; without this it is 1 + 24*4 queries.
     * The "next session" part cannot be solved by eager loading at all, which
     * is why courses.next_session_at is denormalised.
     */
    public function scopeWithCardRelations(Builder $query): Builder
    {
        return $query->with([
            'scheme',
            'subcategory:id,name,slug,course_category_id',
            'subcategory.category:id,name,slug,color_token',
            'deliveryModes:id,name,slug,icon',
        ]);
    }

    public function scopeInCity(Builder $query, string $citySlug): Builder
    {
        return $query->whereHas(
            'schedules',
            fn (Builder $q) => $q->upcoming()->bookable()
                ->whereHas('city', fn (Builder $c) => $c->where('slug', $citySlug))
        );
    }

    public function scopeInCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas(
            'subcategory.category',
            fn (Builder $q) => $q->where('slug', $categorySlug)
        );
    }

    public function scopeWithDeliveryMode(Builder $query, array $slugs): Builder
    {
        return $query->whereHas(
            'deliveryModes',
            fn (Builder $q) => $q->whereIn('slug', $slugs)
        );
    }

    /* --------------------------------------------------- computed attributes */

    /**
     * The title as the public sees it.
     *
     * While a certification scheme is unlicensed, the title carries an
     * "— Exam Preparation" suffix. Flipping the scheme to `accredited`
     * removes it everywhere at once, with no content editing.
     */
    protected function displayTitle(): Attribute
    {
        return Attribute::get(function (): string {
            $scheme = $this->scheme;

            return $scheme && $scheme->status->isIndependent()
                ? $scheme->reposition($this->title)
                : $this->title;
        });
    }

    /** The bare topic, with any scheme suffix stripped — used in prose. */
    protected function topic(): Attribute
    {
        return Attribute::get(fn (): string => trim(explode('—', $this->title)[0]));
    }

    /**
     * Cities are DERIVED from real scheduled sessions and never stored.
     *
     * Storing them would let a course claim a city it has not run in for a
     * year, which is exactly how a landing page becomes a doorway page.
     */
    protected function availableCities(): Attribute
    {
        return Attribute::get(fn (): Collection => $this->schedules()
            ->upcoming()->bookable()->with('city')->get()
            ->pluck('city')->filter()->unique('id')->values());
    }

    protected function nextSession(): Attribute
    {
        return Attribute::get(fn (): ?CourseSchedule => $this->schedules()
            ->upcoming()->bookable()->orderBy('starts_at')->with('city')->first());
    }

    /**
     * Never returns a rating without verified reviews behind it.
     *
     * null renders nothing — not "no reviews yet", not zero stars. A
     * self-declared score is worth less than none to a corporate buyer who
     * can see the reviews are hosted by the seller.
     */
    protected function rating(): Attribute
    {
        return Attribute::get(function (): ?float {
            $verified = $this->testimonials()
                ->where('is_verified', true)
                ->whereNotNull('rating');

            $count = $verified->count();

            return $count >= config('academia.reviews.min_reviews')
                ? round((float) $verified->avg('rating'), 1)
                : null;
        });
    }

    protected function hasSelfPaced(): Attribute
    {
        return Attribute::get(fn (): bool => $this->self_paced_price_cents > 0);
    }

    protected function requiresQuote(): Attribute
    {
        return Attribute::get(fn (): bool => $this->price_cents === null);
    }

    public function isPublished(): bool
    {
        return $this->status === CourseStatus::Published
            && $this->published_at !== null
            && $this->published_at->isPast();
    }
}
