<?php

declare(strict_types=1);

namespace App\Domain\Leads\Models;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CorporateInquiry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'company_name', 'sector', 'company_size', 'country_id', 'city_id',
        'contact_name', 'job_title', 'email', 'phone', 'participants',
        'delivery_mode_id', 'course_id', 'topic', 'preferred_start_date',
        'preferred_window', 'budget_range', 'message', 'status', 'assigned_to',
        'estimated_value_cents', 'won_value_cents', 'lost_reason',
        'first_response_due_at', 'first_responded_at',
        'source', 'utm_source', 'utm_medium', 'utm_campaign',
        'consented_at', 'consent_ip', 'consent_version',
    ];

    protected function casts(): array
    {
        return [
            'status' => LeadStatus::class,
            'preferred_start_date' => 'date',
            'first_response_due_at' => 'datetime',
            'first_responded_at' => 'datetime',
            'consented_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (CorporateInquiry $inquiry): void {
            $inquiry->uuid ??= (string) Str::uuid();
        });
    }

    /* ------------------------------------------------------------ relations */

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function deliveryMode(): BelongsTo
    {
        return $this->belongsTo(DeliveryMode::class);
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(LeadStatusChange::class)->latest('created_at');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable')->latest();
    }

    /* --------------------------------------------------------------- scopes */

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            LeadStatus::Won, LeadStatus::Lost, LeadStatus::Closed,
        ]);
    }

    /**
     * Leads whose first-response SLA has expired without a reply.
     *
     * This is the query the dashboard leads with. A lead form without a
     * measured first-response time generates leads nobody answers.
     */
    public function scopeOverdueSla(Builder $query): Builder
    {
        return $query->whereNull('first_responded_at')
            ->whereNotNull('first_response_due_at')
            ->where('first_response_due_at', '<', now());
    }

    public function scopeAssignedTo(Builder $query, User $user): Builder
    {
        return $query->where('assigned_to', $user->id);
    }

    /* --------------------------------------------------- computed attributes */

    protected function isOverdue(): Attribute
    {
        return Attribute::get(fn (): bool => $this->first_responded_at === null
            && $this->first_response_due_at !== null
            && $this->first_response_due_at->isPast());
    }

    protected function daysOpen(): Attribute
    {
        return Attribute::get(fn (): int => (int) $this->created_at->diffInDays($this->status->isOpen() ? now() : $this->updated_at));
    }

    /** Rough value used for pipeline weighting before a proposal exists. */
    protected function indicativeValueCents(): Attribute
    {
        return Attribute::get(function (): ?int {
            if ($this->won_value_cents) {
                return $this->won_value_cents;
            }

            if ($this->estimated_value_cents) {
                return $this->estimated_value_cents;
            }

            $dayRate = $this->course?->day_rate_cents;

            return $dayRate ? (int) round($dayRate * (float) ($this->course?->duration_days ?? 1)) : null;
        });
    }
}
