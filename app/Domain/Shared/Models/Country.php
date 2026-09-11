<?php

declare(strict_types=1);

namespace App\Domain\Shared\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    /** Factories live in Database\Factories, outside this model's namespace. */
    protected static function newFactory(): Factory
    {
        return CountryFactory::new();
    }

    protected $fillable = [
        'name', 'iso2', 'slug', 'currency', 'vat_rate', 'timezone', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'vat_rate' => 'decimal:2'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Countries with at least one city that has a real upcoming session. */
    public function scopeWithUpcomingTraining(Builder $query): Builder
    {
        return $query->whereHas('cities', fn (Builder $c) => $c->hasUpcomingSessions());
    }
}
