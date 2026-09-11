<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    protected $table = 'seo_metadata';

    protected $fillable = [
        'seoable_type', 'seoable_id', 'title', 'description', 'keywords',
        'og_title', 'og_description', 'og_image_path', 'canonical_url',
        'robots', 'schema_overrides',
    ];

    protected function casts(): array
    {
        return ['schema_overrides' => 'array'];
    }

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
