<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Right to erasure.
 *
 * Anonymises rather than deletes, because booking and certificate records
 * must be retained for seven years under Dutch tax and administration law
 * (Art. 52 AWR). Erasure under GDPR Art. 17 does not override a legal
 * retention obligation — but the personal identifiers can and must go, which
 * is exactly what this does.
 */
class GdprForget extends Command
{
    protected $signature = 'gdpr:forget {email} {--confirm : Actually perform the erasure}';

    protected $description = 'Anonymise all personal data held for an email address.';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));

        if (! $this->option('confirm')) {
            $this->warn('Dry run. Re-run with --confirm to perform the erasure.');
        }

        $redacted = 0;

        DB::transaction(function () use ($email, &$redacted): void {
            $anonymous = [
                'name'    => 'Erased',
                'email'   => 'erased+' . substr(hash('sha256', $email), 0, 12) . '@invalid',
                'phone'   => null,
                'message' => null,
            ];

            if (! $this->option('confirm')) {
                $redacted = Registration::where('email', $email)->withTrashed()->count()
                    + CorporateInquiry::where('email', $email)->withTrashed()->count()
                    + IndividualLead::where('email', $email)->withTrashed()->count();

                return;
            }

            $redacted += Registration::where('email', $email)->withTrashed()
                ->update([...$anonymous, 'company' => null, 'dietary_requirements' => null]);

            $redacted += CorporateInquiry::where('email', $email)->withTrashed()
                ->update(['contact_name' => 'Erased', 'email' => $anonymous['email'],
                          'phone' => null, 'message' => null, 'job_title' => null]);

            $redacted += IndividualLead::where('email', $email)->withTrashed()
                ->update($anonymous);
        });

        $this->info(($this->option('confirm') ? 'Anonymised ' : 'Would anonymise ')
            . "{$redacted} record(s) for {$email}.");

        if ($this->option('confirm')) {
            activity()->withProperties(['records' => $redacted])->log('GDPR erasure');
        }

        return self::SUCCESS;
    }
}
