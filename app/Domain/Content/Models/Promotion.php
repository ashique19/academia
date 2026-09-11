<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A published, time-boxed promotional campaign.
 *
 * `reason` and `ends_at` are NOT NULL in the schema. That is rule 1 of the
 * promotion policy expressed as a constraint: an evergreen sale cannot be
 * created, even by someone writing SQL directly.
 */
class Promotion extends Model
{
    protected $fillable = [
        'name', 'code', 'percentage', 'type', 'reason', 'starts_at', 'ends_at',
        'blurb', 'applies_to_category_slugs', 'is_active', 'max_uses', 'uses_count',
    ];

    protected function casts(): array
    {
        return [
            'applies_to_category_slugs' => 'array',
            'is_active'                 => 'boolean',
            'starts_at'                 => 'datetime',
            'ends_at'                   => 'datetime',
        ];
    }

    /**
     * Live campaigns only. The moment ends_at passes, this returns nothing
     * and the campaign disappears from the banner, the cards, the offers page
     * and the schema simultaneously.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    /** An empty category list means the whole catalogue. */
    public function appliesToCategory(?string $categorySlug): bool
    {
        $slugs = $this->applies_to_category_slugs;

        if (blank($slugs)) {
            return true;
        }

        return $categorySlug !== null && in_array($categorySlug, $slugs, true);
    }

    public function isRunning(): bool
    {
        return $this->is_active
            && $this->starts_at->isPast()
            && $this->ends_at->isFuture();
    }
}
