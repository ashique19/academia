<?php

declare(strict_types=1);

namespace App\Domain\Leads\Models;

use App\Domain\Leads\Enums\RegistrationStatus;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * The join between a person and a session.
 *
 * Phase 3 hangs LMS progress, attendance and certificate issuance off this
 * row, so bookings are deliberately not modelled as anything else.
 */
class Registration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid', 'course_schedule_id', 'user_id', 'name', 'email', 'phone',
        'company', 'job_title', 'country_id', 'message', 'dietary_requirements',
        'seats', 'status', 'price_paid_cents', 'discount_code', 'discount_percent',
        'source', 'utm_source', 'utm_medium', 'utm_campaign',
        'consented_at', 'consent_ip', 'consent_version',
    ];

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'consented_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (Registration $registration): void {
            $registration->uuid ??= (string) Str::uuid();
        });
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'course_schedule_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /** Registrations that currently occupy a seat. */
    public function scopeHoldingSeat(Builder $query): Builder
    {
        return $query->whereIn('status', [
            RegistrationStatus::Interest,
            RegistrationStatus::Pending,
            RegistrationStatus::Confirmed,
            RegistrationStatus::Attended,
        ]);
    }

    public function scopeForEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }
}
