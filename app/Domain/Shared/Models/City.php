<?php

declare(strict_types=1);

namespace App\Domain\Shared\Models;

use App\Domain\Content\Models\SeoMetadata;
use App\Domain\Scheduling\Models\CourseSchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class City extends Model
{
    use HasFactory;

    /** Factories live in Database\Factories, outside this model's namespace. */
    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\CityFactory::new();
    }
    protected $fillable = [
        'country_id', 'name', 'slug', 'intro', 'description',
        'latitude', 'longitude', 'image_path', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeHasUpcomingSessions(Builder $query): Builder
    {
        return $query->whereHas('schedules', fn (Builder $s) => $s->upcoming()->bookable());
    }

    public function scopeWithUpcomingSessionCount(Builder $query): Builder
    {
        return $query->withCount([
            'schedules as upcoming_sessions_count' => fn (Builder $s) => $s->upcoming()->bookable(),
        ]);
    }

    /**
     * THE DOORWAY-PAGE GATE.
     *
     * A city page renders only where a real, scheduled, bookable session
     * exists. Anything else 404s. That is the line between a local landing
     * page and a doorway page, and doorway pages are a manual-action
     * category — so the check lives in code, where growth pressure cannot
     * quietly forget it.
     */
    public function hasUpcomingSessions(): bool
    {
        return $this->schedules()->upcoming()->bookable()->exists();
    }

    /** Cities within roughly this many km — for the "nearby" block. */
    public function nearby(int $limit = 4, int $km = 400): \Illuminate\Support\Collection
    {
        if ($this->latitude === null || $this->longitude === null) {
            return collect();
        }

        return static::query()
            ->active()
            ->whereKeyNot($this->getKey())
            ->hasUpcomingSessions()
            ->with('country')
            ->get()
            ->map(function (City $city): City {
                $city->distance_km = $this->distanceTo($city);

                return $city;
            })
            ->filter(fn (City $city) => $city->distance_km <= $km)
            ->sortBy('distance_km')
            ->take($limit)
            ->values();
    }

    /** Haversine, in kilometres. */
    public function distanceTo(City $other): float
    {
        $earthRadius = 6371;

        $latFrom = deg2rad((float) $this->latitude);
        $lonFrom = deg2rad((float) $this->longitude);
        $latTo   = deg2rad((float) $other->latitude);
        $lonTo   = deg2rad((float) $other->longitude);

        $angle = 2 * asin(sqrt(
            sin(($latTo - $latFrom) / 2) ** 2 +
            cos($latFrom) * cos($latTo) * sin(($lonTo - $lonFrom) / 2) ** 2
        ));

        return round($angle * $earthRadius, 1);
    }

    /** True while the hand-written intro is missing — surfaced in admin. */
    protected function needsContent(): Attribute
    {
        return Attribute::get(fn (): bool => blank($this->intro));
    }
}
