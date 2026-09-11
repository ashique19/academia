<?php

declare(strict_types=1);

namespace App\Domain\Leads\Services;

/**
 * Honeypot and timing checks for public forms.
 *
 * Deliberately NOT a CAPTCHA. CAPTCHA measurably costs conversion on exactly
 * the high-value B2B forms this site depends on, and a honeypot plus a timing
 * floor stops the overwhelming majority of automated submissions.
 *
 * Both checks fail SILENTLY — the caller redirects to the thank-you page as
 * though the submission succeeded. A bot told it was detected adapts; a bot
 * that believes it succeeded does not.
 */
class SpamGuard
{
    /**
     * @param  string  $honeypot     Value of the hidden field. Must be empty.
     * @param  int     $renderedAt   Unix timestamp when the form was rendered.
     */
    public function looksAutomated(string $honeypot, int $renderedAt): bool
    {
        if ($honeypot !== '') {
            return true;
        }

        $minimum = (int) config('academia.leads.min_form_seconds', 3);

        // A zero timestamp means the field was stripped — treat as suspicious.
        if ($renderedAt <= 0) {
            return true;
        }

        return (now()->timestamp - $renderedAt) < $minimum;
    }

    /**
     * Free-provider check for B2B forms.
     *
     * Warns, never blocks. Plenty of legitimate small-company buyers use
     * Gmail, and blocking them to filter noise loses real revenue.
     */
    public function isFreeEmailProvider(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, '@') ?: '', 1));

        return in_array($domain, [
            'gmail.com', 'googlemail.com', 'hotmail.com', 'outlook.com',
            'live.com', 'yahoo.com', 'icloud.com', 'proton.me', 'protonmail.com',
            'gmx.net', 'web.de', 'aol.com',
        ], true);
    }
}
