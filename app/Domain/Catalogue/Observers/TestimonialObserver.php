<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Observers;

use App\Domain\Content\Models\Testimonial;
use DomainException;

/**
 * Enforces the verification gate at the model boundary.
 *
 * This lives in an observer rather than a form request deliberately: a
 * validation rule protects one form, an observer protects every write path —
 * including the admin panel, a seeder, an import and a future API.
 */
class TestimonialObserver
{
    public function saving(Testimonial $testimonial): void
    {
        if ($testimonial->status !== 'published') {
            return;
        }

        if (! $testimonial->isPublishable()) {
            throw new DomainException(
                'A testimonial cannot be published unless it is verified with a '
                . 'consent reference on file, or explicitly flagged as illustrative. '
                . 'See the review policy in the build documentation.'
            );
        }

        // An illustrative quote must not carry a personal attribution, or the
        // marker and the byline say different things on the same card.
        if ($testimonial->is_illustrative) {
            $testimonial->author_name = null;
            $testimonial->organisation = null;
        }
    }
}
