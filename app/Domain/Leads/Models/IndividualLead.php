<?php

declare(strict_types=1);

namespace App\Domain\Leads\Models;

use App\Domain\Catalogue\Models\Course;
use App\Domain\Leads\Enums\LeadSource;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class IndividualLead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'name', 'email', 'phone', 'country_id', 'city_id', 'course_id',
        'course_schedule_id', 'preferred_date', 'message', 'source', 'status',
        'assigned_to', 'utm_source', 'utm_medium', 'utm_campaign',
        'consented_at', 'consent_ip', 'consent_version',
        'confirmed_at', 'confirmation_token',
    ];

    protected function casts(): array
    {
        return [
            'source' => LeadSource::class,
            'preferred_date' => 'date',
            'consented_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    protected $hidden = ['confirmation_token'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (IndividualLead $lead): void {
            $lead->uuid ??= (string) Str::uuid();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeFromSource(Builder $query, LeadSource $source): Builder
    {
        return $query->where('source', $source);
    }

    /** Newsletter leads are only real once double opt-in completes. */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->whereNotNull('confirmed_at');
    }
}
