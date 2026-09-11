<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use App\Domain\Catalogue\Models\CourseSubcategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlossaryTerm extends Model
{
    protected $fillable = [
        'term', 'slug', 'definition', 'body', 'course_subcategory_id', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(CourseSubcategory::class, 'course_subcategory_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
