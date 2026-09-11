<?php

declare(strict_types=1);

namespace App\Domain\Scheduling\Models;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Leads\Models\Registration;
use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use App\Domain\Shared\Models\Venue;
use Database\Factories\CourseScheduleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A dated instance of a course.
 *
 * ---------------------------------------------------------------------------
 * SESSIONS HAVE NO PUBLIC URL. This is deliberate and load-bearing.
 *
 * There are 1,561 of these and more are added continuously. Giving each one a
 * routable page would create thousands of URLs that die on their end date —
 * the course page is the evergreen asset that accumulates authority, and
 * session pages would leak it away into 404s.
 *
 * Sessions surface INSIDE course pages, the schedule view and city pages.
 * There is intentionally no getRouteKeyName() and no route bound to this model.
 * ---------------------------------------------------------------------------
 */
class CourseSchedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** Factories live in Database\Factories, outside this model's namespace. */
    protected static function newFactory(): Factory
    {
        return CourseScheduleFactory::new();
    }

    protected $fillable = [
        'reference', 'course_id', 'delivery_mode_id', 'country_id', 'city_id',
        'venue_id', 'trainer_id', 'starts_at', 'ends_at', 'timezone',
        'seat_limit', 'seats_taken', 'price_cents', 'currency', 'status',
        'language', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'status' => ScheduleStatus::class,
        ];
    }

    /* ------------------------------------------------------------ relations */

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function deliveryMode(): BelongsTo
    {
        return $this->belongsTo(DeliveryMode::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /* --------------------------------------------------------------- scopes */

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('starts_at', '>', now());
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now());
    }

    /** Only Open sessions accept a booking. */
    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('status', ScheduleStatus::Open);
    }

    /** Open and Full both appear publicly — Full gets a waitlist CTA. */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->whereIn('status', [ScheduleStatus::Open, ScheduleStatus::Full]);
    }

    public function scopeInCity(Builder $query, string $slug): Builder
    {
        return $query->whereHas('city', fn (Builder $c) => $c->where('slug', $slug));
    }

    public function scopeInCountry(Builder $query, string $slug): Builder
    {
        return $query->whereHas('country', fn (Builder $c) => $c->where('slug', $slug));
    }

    public function scopeBetweenDates(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('starts_at', [$from, $to]);
    }

    public function scopeWithListRelations(Builder $query): Builder
    {
        return $query->with([
            'course:id,title,slug,duration_days,level,price_cents,course_subcategory_id,certification_scheme_id',
            'course.scheme',
            'course.subcategory:id,name,slug,course_category_id',
            'course.subcategory.category:id,name,slug',
            'city:id,name,slug,country_id',
            'city.country:id,name,slug,iso2',
            'deliveryMode:id,name,slug,icon',
            'venue:id,name,city_id',
        ]);
    }

    /* --------------------------------------------------- computed attributes */

    /**
     * Never a column. seats_taken is the counter; availability is derived,
     * clamped at zero so a data anomaly can never render a negative.
     */
    protected function seatsAvailable(): Attribute
    {
        return Attribute::get(fn (): int => max(0, $this->seat_limit - $this->seats_taken));
    }

    protected function isFull(): Attribute
    {
        return Attribute::get(fn (): bool => $this->seats_available <= 0);
    }

    /** "Only 3 seats left" is honest below this; above it, it is a dark pattern. */
    protected function isNearlyFull(): Attribute
    {
        return Attribute::get(fn (): bool => $this->seats_available > 0 && $this->seats_available <= 3);
    }

    protected function durationDays(): Attribute
    {
        return Attribute::get(fn (): int => (int) $this->starts_at->diffInDays($this->ends_at) + 1);
    }

    /** Location as the public sees it — onsite and online are not cities. */
    protected function locationLabel(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->city) {
                return $this->city->name;
            }

            return match ($this->deliveryMode?->slug) {
                'onsite' => 'Your premises',
                'online' => 'Live online',
                'self-paced' => 'Self-paced',
                default => 'Live online',
            };
        });
    }

    public function isBookable(): bool
    {
        return $this->status->isBookable()
            && $this->starts_at->isFuture()
            && $this->seats_available > 0;
    }

    /**
     * Start time in the viewer's zone.
     *
     * Storage is UTC; display converts. A participant joining an hour late
     * because the site showed CET without saying so is a refund.
     */
    public function startsAtIn(string $timezone): Carbon
    {
        return $this->starts_at->copy()->setTimezone($timezone);
    }

    public function effectivePriceCents(): ?int
    {
        return $this->price_cents ?? $this->course?->price_cents;
    }
}
