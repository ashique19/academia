<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * Data-retention enforcement (spec §22.1).
 *
 * The privacy policy promises two windows, and this command is what makes
 * them true rather than aspirational:
 *
 *  - Unconverted enquiries are anonymised 24 months after they were created.
 *  - Booking records are kept for seven years (Dutch tax/administration law,
 *    Art. 52 AWR) and then have their personal identifiers stripped.
 *
 * Like gdpr:forget this anonymises in place rather than deleting: the row and
 * its non-personal, statistically useful columns survive, but nothing that
 * identifies a person does. It is idempotent — already-anonymised rows carry
 * the sentinel email domain and are skipped on the next run.
 */
class PurgeExpiredLeads extends Command
{
    protected $signature = 'academia:purge-expired-leads {--dry-run : Report what would be anonymised without writing}';

    protected $description = 'Anonymise personal data past its retention window (spec §22.1).';

    /** Anonymised rows are tagged with this address domain and never re-touched. */
    private const ERASED_DOMAIN = '@invalid';

    public function handle(): int
    {
        $dryRun         = (bool) $this->option('dry-run');
        $deadLeadCutoff = now()->subMonths((int) config('academia.retention.dead_leads_months'));
        $bookingCutoff  = now()->subYears((int) config('academia.retention.bookings_years'));

        if ($dryRun) {
            $this->warn('Dry run. Re-run without --dry-run to anonymise.');
        }

        // Unconverted corporate inquiries: everything that never reached Won.
        $inquiries = $this->anonymise(
            CorporateInquiry::query()
                ->withTrashed()
                ->where('status', '!=', LeadStatus::Won)
                ->where('created_at', '<', $deadLeadCutoff),
            fn (CorporateInquiry $row): array => [
                'contact_name' => 'Erased',
                'email'        => $this->erasedEmail($row->email),
                'phone'        => null,
                'job_title'    => null,
                'message'      => null,
            ],
            $dryRun,
        );

        // Individual course enquiries and newsletter leads.
        $leads = $this->anonymise(
            IndividualLead::query()
                ->withTrashed()
                ->where('created_at', '<', $deadLeadCutoff),
            fn (IndividualLead $row): array => [
                'name'    => 'Erased',
                'email'   => $this->erasedEmail($row->email),
                'phone'   => null,
                'message' => null,
            ],
            $dryRun,
        );

        // Bookings past the seven-year statutory window. The financial columns
        // (price_paid_cents, seats, dates) stay; the person does not.
        $bookings = $this->anonymise(
            Registration::query()
                ->withTrashed()
                ->where('created_at', '<', $bookingCutoff),
            fn (Registration $row): array => [
                'name'                 => 'Erased',
                'email'                => $this->erasedEmail($row->email),
                'phone'                => null,
                'company'              => null,
                'job_title'            => null,
                'message'              => null,
                'dietary_requirements' => null,
            ],
            $dryRun,
        );

        $this->newLine();
        $this->line(($dryRun ? 'Would anonymise' : 'Anonymised')
            . " — corporate inquiries: {$inquiries}, individual leads: {$leads}, bookings: {$bookings}.");

        if (! $dryRun && ($inquiries + $leads + $bookings) > 0) {
            activity()->withProperties(compact('inquiries', 'leads', 'bookings'))
                ->log('Retention anonymisation');
        }

        return self::SUCCESS;
    }

    /**
     * Anonymise every row the builder matches that has not already been erased.
     *
     * @param  callable(\Illuminate\Database\Eloquent\Model): array<string, mixed>  $attributes
     */
    private function anonymise(Builder $query, callable $attributes, bool $dryRun): int
    {
        $query->where('email', 'not like', '%' . self::ERASED_DOMAIN);

        if ($dryRun) {
            return $query->count();
        }

        $count = 0;

        $query->chunkById(500, function ($rows) use ($attributes, &$count): void {
            foreach ($rows as $row) {
                $row->forceFill($attributes($row))->saveQuietly();
                $count++;
            }
        });

        return $count;
    }

    /** Deterministic, non-reversible replacement that preserves row-uniqueness. */
    private function erasedEmail(?string $email): string
    {
        return 'erased+' . substr(hash('sha256', (string) $email . 'retention'), 0, 12) . self::ERASED_DOMAIN;
    }
}
