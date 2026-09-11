<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Models;

use App\Domain\Catalogue\Enums\SchemeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A third-party certification scheme — PRINCE2, PMP, ITIL, TOGAF and 19 more.
 *
 * All are trademarked and require an accredited-training-organisation licence
 * that Academia does not hold. Selling them as if we could issue the
 * certificate is both a trademark exposure and a deal-loss risk the moment a
 * buyer checks.
 *
 * `status` is the whole mechanism: flip it to `accredited` the day a licence
 * lands and the title suffix, the trademark notice and the schema all change
 * at once.
 */
class CertificationScheme extends Model
{
    protected $fillable = [
        'name', 'slug', 'owner', 'status', 'match_needle',
        'exam_questions', 'exam_format', 'exam_pass_mark', 'exam_duration',
        'exam_book', 'pathway',
    ];

    protected function casts(): array
    {
        return ['status' => SchemeStatus::class];
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function isIndependent(): bool
    {
        return $this->status->isIndependent();
    }

    /**
     * Add the "— Exam Preparation" suffix unless the title already says so.
     *
     * Idempotent: repositioning an already-repositioned title is a no-op,
     * which matters because this runs on every render.
     */
    public function reposition(string $title): string
    {
        if (! $this->isIndependent()) {
            return $title;
        }

        $lowered = strtolower($title);

        if (str_contains($lowered, 'exam preparation')
            || str_contains($lowered, 'certification preparation')) {
            return $title;
        }

        if (str_ends_with($lowered, ' preparation')) {
            return str_ireplace(' Preparation', ' — Exam Preparation', $title);
        }

        return $title.' — Exam Preparation';
    }

    /** The notice rendered on every course page carrying this scheme. */
    public function trademarkNotice(): string
    {
        return sprintf(
            'This is an independent preparation course. It is not accredited by, '
            .'affiliated with or endorsed by %1$s, and Academia does not issue the '
            .'%2$s certificate. You book and sit the official exam with %1$s or an '
            .'accredited examination centre — we will tell you exactly how, and our '
            .'fee does not include the exam.',
            $this->owner,
            $this->name
        );
    }

    public function trademarkAttribution(): string
    {
        return sprintf('%s is a registered trade mark of %s. All rights reserved.',
            $this->name, $this->owner);
    }
}
