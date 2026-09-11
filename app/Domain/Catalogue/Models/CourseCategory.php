<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Models;

use App\Domain\Content\Models\SeoMetadata;
use Database\Factories\CourseCategoryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class CourseCategory extends Model
{
    use HasFactory;

    /** Factories live in Database\Factories, outside this model's namespace. */
    protected static function newFactory(): Factory
    {
        return CourseCategoryFactory::new();
    }

    protected $fillable = [
        'name', 'slug', 'summary', 'description', 'icon', 'color_token',
        'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(CourseSubcategory::class)->orderBy('sort_order');
    }

    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Course::class,
            CourseSubcategory::class,
            'course_category_id',
            'course_subcategory_id'
        );
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Manual order — categories are merchandised, never alphabetised. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithPublishedCourseCount(Builder $query): Builder
    {
        return $query->withCount([
            'courses as published_courses_count' => fn (Builder $q) => $q->published(),
        ]);
    }
}
