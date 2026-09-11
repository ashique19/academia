<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Leads\Models\CorporateInquiry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'job_title',
        'organisation_id', 'trainer_id', 'locale', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    public function assignedInquiries(): HasMany
    {
        return $this->hasMany(CorporateInquiry::class, 'assigned_to');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Roles that reach the admin panel. */
    public function scopeStaff(Builder $query): Builder
    {
        return $query->whereHas('roles', fn (Builder $r) => $r->whereIn('name', [
            'super-admin', 'admin', 'course-manager', 'sales-manager', 'content-editor',
        ]));
    }

    public function canAccessAdmin(): bool
    {
        return $this->is_active && $this->hasAnyRole([
            'super-admin', 'admin', 'course-manager', 'sales-manager', 'content-editor',
        ]);
    }

    /**
     * 2FA is mandatory for every role that can see personal data or change
     * a published price. Enforced by middleware, declared here.
     */
    public function requiresTwoFactor(): bool
    {
        return $this->hasAnyRole(['super-admin', 'admin', 'course-manager', 'sales-manager']);
    }
}
