<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Models;

use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A subject-matter expert.
 *
 * ---------------------------------------------------------------------------
 * VISIBILITY IS AN OPEN BUSINESS DECISION — see spec §3.1
 *
 * V2 of this product required that trainer identities are NOT published
 * before a booking is confirmed; the site sells the *standard* (10+ years,
 * still practising, reference-checked) and releases the named profile with
 * the joining instructions. The rebuild brief asks for public profiles with
 * photos and LinkedIn URLs. Both are supported here.
 *
 * `is_public` defaults to false, and every public-facing query goes through
 * scopePublic(). If the anonymous position is kept, no template changes are
 * needed — the /trainers route simply 404s because the scope returns nothing.
 * ---------------------------------------------------------------------------
 */
class Trainer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference', 'name', 'slug', 'headline', 'bio_short', 'bio_full',
        'years_experience', 'certifications', 'languages', 'linkedin_url',
        'city_id', 'is_public', 'published_at', 'status',
        'day_rate_cents', 'contract_type', 'availability_notes',
        'reference_checked_at', 'internal_rating', 'days_delivered',
    ];

    /**
     * Internal commercial data. Hidden at the model level as a second line of
     * defence: even a careless ->toJson() in a future component cannot leak
     * a day rate to the browser.
     */
    protected $hidden = [
        'day_rate_cents', 'contract_type', 'availability_notes',
        'internal_rating', 'reference_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'certifications' => 'array',
            'languages' => 'array',
            'is_public' => 'boolean',
            'published_at' => 'datetime',
            'reference_checked_at' => 'date',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)->withPivot('is_lead');
    }

    public function expertise(): BelongsToMany
    {
        return $this->belongsToMany(CourseSubcategory::class, 'course_subcategory_trainer');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * The ONLY scope any public surface may use.
     *
     * Three conditions, all required. A trainer marked public but never
     * published, or marked public then deactivated, stays invisible.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true)
            ->whereNotNull('published_at')
            ->where('status', 'active');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * The anonymised credential line used wherever a name is not published.
     *
     * This is the V2 position expressed as content: it makes a claim about
     * the standard, which is substantiable, rather than about a person.
     */
    protected function anonymousCredential(): Attribute
    {
        return Attribute::get(function (): string {
            $years = $this->years_experience ?? 10;

            return sprintf(
                'A subject-matter expert with %d+ years in the field, still practising, '
                .'reference-checked and matched to your cohort.',
                $years
            );
        });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_public
            && $this->published_at !== null
            && $this->status === 'active';
    }
}
