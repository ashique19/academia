<?php

declare(strict_types=1);

namespace App\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venue extends Model
{
    protected $fillable = [
        'city_id', 'name', 'address_line1', 'address_line2', 'postcode',
        'transport_notes', 'facilities', 'accessibility_notes', 'capacity', 'is_active',
    ];

    protected function casts(): array
    {
        return ['facilities' => 'array', 'is_active' => 'boolean'];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::get(fn (): string => collect([
            $this->address_line1,
            $this->address_line2,
            $this->postcode,
            $this->city?->name,
        ])->filter()->implode(', '));
    }
}
