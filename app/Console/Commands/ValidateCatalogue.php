<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Referential integrity gate.
 *
 * Runs after every import and in CI. Exits non-zero on failure so a broken
 * catalogue cannot be deployed — an earlier version of this product shipped
 * with 519 sessions pointing at an unknown city, and nothing caught it until
 * the city pages were empty in production.
 */
class ValidateCatalogue extends Command
{
    protected $signature = 'academia:validate {--strict : Treat warnings as failures}';

    protected $description = 'Check catalogue referential integrity and publishing readiness.';

    public function handle(): int
    {
        $errors = [
            'Orphan sessions (course missing)' =>
                'SELECT COUNT(*) FROM course_schedules s LEFT JOIN courses c ON c.id = s.course_id WHERE c.id IS NULL',

            'Classroom sessions with no city' =>
                'SELECT COUNT(*) FROM course_schedules s
                 JOIN delivery_modes m ON m.id = s.delivery_mode_id
                 WHERE m.slug = \'classroom\' AND s.city_id IS NULL',

            'Sessions oversold (seats_taken > seat_limit)' =>
                'SELECT COUNT(*) FROM course_schedules WHERE seats_taken > seat_limit',

            'Sessions ending before they start' =>
                'SELECT COUNT(*) FROM course_schedules WHERE ends_at <= starts_at',

            'Published courses with no delivery mode' =>
                'SELECT COUNT(*) FROM courses c WHERE c.status = \'published\'
                 AND NOT EXISTS (SELECT 1 FROM course_delivery_mode d WHERE d.course_id = c.id)',

            'Categories with no published courses' =>
                'SELECT COUNT(*) FROM course_categories cc WHERE NOT EXISTS (
                     SELECT 1 FROM course_subcategories s
                     JOIN courses c ON c.course_subcategory_id = s.id
                     WHERE s.course_category_id = cc.id AND c.status = \'published\')',

            'Testimonials published without verification or illustrative flag' =>
                'SELECT COUNT(*) FROM testimonials
                 WHERE status = \'published\' AND is_verified = 0 AND is_illustrative = 0',
        ];

        $warnings = [
            'Cities with no hand-written intro' =>
                'SELECT COUNT(*) FROM cities WHERE is_active = 1 AND (intro IS NULL OR intro = \'\')',

            'Active cities with no upcoming open session (city page will 404)' =>
                'SELECT COUNT(*) FROM cities ci WHERE ci.is_active = 1 AND NOT EXISTS (
                     SELECT 1 FROM course_schedules s
                     WHERE s.city_id = ci.id AND s.status = \'open\' AND s.starts_at > CURRENT_TIMESTAMP)',

            'Published courses with no upcoming session' =>
                'SELECT COUNT(*) FROM courses c WHERE c.status = \'published\'
                 AND c.next_session_at IS NULL',

            'Published courses with no SEO description' =>
                'SELECT COUNT(*) FROM courses c WHERE c.status = \'published\'
                 AND NOT EXISTS (SELECT 1 FROM seo_metadata m
                     WHERE m.seoable_id = c.id AND m.seoable_type LIKE \'%Course\'
                     AND m.description IS NOT NULL)',

            'Published courses with no syllabus modules' =>
                'SELECT COUNT(*) FROM courses c WHERE c.status = \'published\'
                 AND NOT EXISTS (SELECT 1 FROM course_modules m WHERE m.course_id = c.id)',
        ];

        $failed = $this->run('Errors', $errors, true);
        $warned = $this->run('Warnings', $warnings, false);

        $this->newLine();

        if ($failed > 0) {
            $this->error("{$failed} integrity error(s). This catalogue must not be deployed.");

            return self::FAILURE;
        }

        if ($warned > 0 && $this->option('strict')) {
            $this->error("{$warned} warning(s), and --strict was set.");

            return self::FAILURE;
        }

        $this->info($warned > 0
            ? "No integrity errors. {$warned} warning(s) — review before launch."
            : 'Catalogue is clean.');

        return self::SUCCESS;
    }

    private public function handle(string $heading, array $checks, bool $isError): int
    {
        $this->newLine();
        $this->line("<fg=white;options=bold>{$heading}</>");

        $count = 0;

        foreach ($checks as $label => $sql) {
            $result = (int) (DB::selectOne($sql)?->{array_key_first((array) DB::selectOne($sql))} ?? 0);

            if ($result === 0) {
                $this->line("  <fg=green>✓</> {$label}");
                continue;
            }

            $count++;
            $marker = $isError ? '<fg=red>✗</>' : '<fg=yellow>!</>';
            $this->line("  {$marker} {$label}: <fg=yellow>{$result}</>");
        }

        return $count;
    }
}
