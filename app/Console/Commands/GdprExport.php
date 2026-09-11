<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Leads\Models\Registration;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * GDPR subject access and erasure.
 *
 * Built in MVP deliberately. Retrofitting erasure across every table that
 * holds personal data, after those tables have grown, is far worse than
 * writing it once while there are only four of them.
 *
 * Both operations are logged, because Article 30 requires records of
 * processing activities and "who exported the lead list" is exactly that.
 */
class GdprExport extends Command
{
    protected $signature = 'gdpr:export {email : The data subject\'s email address}
                                        {--json= : Write the export to this path}';

    protected $description = 'Export every piece of personal data held for an email address.';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));

        $data = [
            'subject' => $email,
            'generated_at' => now()->toIso8601String(),
            'records' => [
                'user' => User::where('email', $email)->get()->toArray(),
                'registrations' => Registration::where('email', $email)->withTrashed()->get()->toArray(),
                'corporate_inquiries' => CorporateInquiry::where('email', $email)->withTrashed()->get()->toArray(),
                'individual_leads' => IndividualLead::where('email', $email)->withTrashed()->get()->toArray(),
            ],
        ];

        $total = collect($data['records'])->sum(fn (array $rows) => count($rows));

        if ($total === 0) {
            $this->warn("No personal data held for {$email}.");

            return self::SUCCESS;
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($path = $this->option('json')) {
            file_put_contents($path, $json);
            $this->info("Exported {$total} record(s) to {$path}.");
        } else {
            $this->line($json);
        }

        activity()
            ->withProperties(['email' => $email, 'records' => $total])
            ->log('GDPR subject access export');

        return self::SUCCESS;
    }
}
