<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Models;

use Database\Factories\DeliveryModeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeliveryMode extends Model
{
    use HasFactory;

    /** Factories live in Database\Factories, outside this model's namespace. */
    protected static function newFactory(): Factory
    {
        return DeliveryModeFactory::new();
    }

    protected $fillable = ['name', 'slug', 'icon', 'description', 'sort_order'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class);
    }
}
