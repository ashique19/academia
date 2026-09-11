<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A client case study.
 *
 * `client_name`, `client_quote` and `client_approved` are deliberately empty
 * at launch. A study publishes a name and a number only once the client has
 * signed both off — the template ships with the slots visible and empty
 * rather than filled with something plausible.
 */
class CaseStudy extends Model
{
    protected $fillable = [
        'title', 'slug', 'sector', 'summary', 'background', 'approach_points',
        'client_name', 'client_quote', 'client_quote_attribution',
        'client_approved', 'status',
    ];

    protected function casts(): array
    {
        return [
            'approach_points' => 'array',
            'client_approved' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /** A named client may only be shown once approval is recorded. */
    public function displayClient(): ?string
    {
        return $this->client_approved ? $this->client_name : null;
    }
}
