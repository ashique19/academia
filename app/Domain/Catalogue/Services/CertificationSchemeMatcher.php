<?php

declare(strict_types=1);

namespace App\Domain\Catalogue\Services;

use App\Domain\Catalogue\Models\CertificationScheme;
use Illuminate\Support\Collection;

/**
 * Matches a course title to a trademarked certification scheme.
 *
 * ---------------------------------------------------------------------------
 * WHY THE MATCHING IS SO FUSSY
 *
 * An earlier version used a naive case-insensitive substring match. It tagged
 * "Building Psychological Safety" as a SAFe(R) course — putting a trademark
 * notice and an exam specification on a page that had no business carrying
 * either. That is precisely the class of error a certification buyer notices
 * immediately, and it damages the credibility of every other claim on the
 * site at the same time.
 *
 * Two rules fix it:
 *   1. Case-sensitive. "SAFe" is a mark; "safe" is an adjective.
 *   2. Word-boundary anchored — but only where the needle's edge character is
 *      alphanumeric. "Business Continuity Management (ISO 22301)" ends in a
 *      parenthesis, and \b after ")" never matches.
 * ---------------------------------------------------------------------------
 */
class CertificationSchemeMatcher
{
    private ?Collection $schemes = null;

    public function match(string $title): ?CertificationScheme
    {
        foreach ($this->schemes() as $scheme) {
            if ($this->matches($title, $scheme->match_needle)) {
                return $scheme;
            }
        }

        return null;
    }

    /**
     * Case-sensitive, word-boundary-anchored containment test.
     *
     * Public and pure so it can be unit-tested directly against the known
     * false positives without touching the database.
     */
    public function matches(string $title, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        // \b is only meaningful next to a word character. Applying it to a
        // needle that starts or ends with punctuation guarantees no match.
        $left  = ctype_alnum($needle[0]) ? '\b' : '';
        $right = ctype_alnum($needle[strlen($needle) - 1]) ? '\b' : '';

        $pattern = '/' . $left . preg_quote($needle, '/') . $right . '/';

        return preg_match($pattern, $title) === 1;
    }

    /** @return Collection<int, CertificationScheme> */
    public function schemes(): Collection
    {
        return $this->schemes ??= CertificationScheme::query()
            // Longest needle first, so "PRINCE2 Agile" wins over "PRINCE2".
            ->orderByRaw('LENGTH(match_needle) DESC')
            ->get();
    }

    public function flush(): void
    {
        $this->schemes = null;
    }
}
