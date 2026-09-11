<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A syllabus module.
 *
 * A real table rather than a JSON blob on the course, because modules get
 * reordered, searched and — when the LMS arrives — mapped one-to-one onto
 * lessons. Storing the outline relationally now means Phase 3 inherits the
 * structure instead of migrating it.
 */
class CourseModule extends Model
{
    protected $fillable = ['course_id', 'title', 'body', 'bullets', 'duration_hours', 'sort_order'];

    protected function casts(): array
    {
        return [
            'bullets' => 'array',
            'duration_hours' => 'decimal:1',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
