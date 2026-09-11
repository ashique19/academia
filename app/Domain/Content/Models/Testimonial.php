<?php

declare(strict_types=1);

namespace App\Domain\Content\Models;

use App\Domain\Catalogue\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A testimonial.
 *
 * ---------------------------------------------------------------------------
 * THE VERIFICATION GATE — see spec §3.1
 *
 * Anonymising an invented quote does not make it true; it makes it harder to
 * disprove. Under the Unfair Commercial Practices Directive as amended by
 * Directive (EU) 2019/2161, presenting consumer reviews without reasonable
 * steps to verify they came from actual customers is a listed unfair
 * practice, and that does not turn on whether a name is attached.
 *
 * So a testimonial may be published in exactly two states:
 *
 *   is_verified     — a real, permissioned quote with a consent reference
 *                     on file. Renders normally.
 *   is_illustrative — describes the outcome a course is DESIGNED to produce.
 *                     Renders with a visible "Illustrative" marker. That is
 *                     a claim about programme design, which is substantiable.
 *
 * Anything else cannot reach `published`. Enforced in TestimonialObserver.
 * ---------------------------------------------------------------------------
 */
class Testimonial extends Model
{
    protected $fillable = [
        'author_name', 'author_role', 'author_sector', 'organisation', 'quote',
        'course_id', 'delivery_mode', 'rating', 'is_verified', 'consent_reference',
        'is_illustrative', 'verified_by', 'verified_at', 'status', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_verified'     => 'boolean',
            'is_illustrative' => 'boolean',
            'verified_at'     => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    public function scopeIllustrative(Builder $query): Builder
    {
        return $query->where('is_illustrative', true);
    }

    /** Everything the public may see: verified, or flagged illustrative. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn (Builder $q) => $q->where('is_verified', true)
                ->orWhere('is_illustrative', true));
    }

    /** Awaiting an Admin decision — the admin verification queue. */
    public function scopeAwaitingVerification(Builder $query): Builder
    {
        return $query->where('is_verified', false)
            ->where('is_illustrative', false);
    }

    /**
     * A testimonial is publishable only in the two sanctioned states.
     * Verified additionally requires the consent reference to be on file:
     * "we asked them" is not evidence, a reference is.
     */
    public function isPublishable(): bool
    {
        if ($this->is_illustrative) {
            return true;
        }

        return $this->is_verified && filled($this->consent_reference);
    }

    /**
     * Attribution as rendered.
     *
     * Illustrative rows never carry a person's name, even if one was typed
     * into the field — the marker and the attribution must not contradict
     * each other.
     */
    protected function attribution(): Attribute
    {
        return Attribute::get(function (): string {
            if ($this->is_illustrative) {
                return collect([$this->author_role, $this->author_sector, $this->delivery_mode])
                    ->filter()->implode(' · ');
            }

            return collect([$this->author_name, $this->author_role, $this->organisation])
                ->filter()->implode(' · ');
        });
    }
}
