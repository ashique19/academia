<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Catalogue\Enums\CourseLevel;
use App\Domain\Catalogue\Enums\CourseStatus;
use App\Domain\Catalogue\Enums\SchemeStatus;
use App\Domain\Catalogue\Models\CertificationScheme;
use App\Domain\Catalogue\Models\Course;
use App\Domain\Catalogue\Models\CourseCategory;
use App\Domain\Catalogue\Models\CourseModule;
use App\Domain\Catalogue\Models\CourseSubcategory;
use App\Domain\Catalogue\Models\DeliveryMode;
use App\Domain\Catalogue\Models\Trainer;
use App\Domain\Content\Models\GlossaryTerm;
use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Models\CourseSchedule;
use App\Domain\Shared\Models\City;
use App\Domain\Shared\Models\Country;
use App\Domain\Shared\Models\Venue;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Imports the real Academia catalogue from CSV.
 *
 * Idempotent by design: every write is an updateOrCreate keyed on a natural
 * business key (course code, session reference, city slug), so the command
 * can be re-run after fixing a row without duplicating anything.
 *
 * --dry-run parses and validates everything and writes nothing, reporting
 * per-row problems rather than aborting on the first one. An importer that
 * dies on row 300 of 524 tells you about one problem per run.
 */
class ImportCatalogue extends Command
{
    protected $signature = 'academia:import
        {--dry-run    : Parse and validate without writing anything}
        {--only=      : Import one section only (reference|trainers|courses|sessions|glossary)}
        {--path=      : Override the CSV directory}';

    protected $description = 'Import the course catalogue, schedule, cities and trainers from CSV.';

    /** Module delimiter in the syllabus column (section sign, U+00A7). */
    private const MODULE_SEPARATOR = "\u{00A7}";

    private string $path;
    private bool $dryRun;

    /** @var array<int, string> */
    private array $problems = [];

    private array $counts = [];

    public function handle(): int
    {
        $this->path   = rtrim($this->option('path') ?: database_path('data'), '/');
        $this->dryRun = (bool) $this->option('dry-run');
        $only         = $this->option('only');

        if (! is_dir($this->path)) {
            $this->error("CSV directory not found: {$this->path}");

            return self::FAILURE;
        }

        if ($this->dryRun) {
            $this->warn('DRY RUN — nothing will be written.');
        }

        $sections = [
            'reference' => fn () => $this->importReference(),
            'trainers'  => fn () => $this->importTrainers(),
            'courses'   => fn () => $this->importCourses(),
            'sessions'  => fn () => $this->importSessions(),
            'glossary'  => fn () => $this->importGlossary(),
        ];

        foreach ($sections as $name => $importer) {
            if ($only && $only !== $name) {
                continue;
            }

            $this->components->task("Importing {$name}", function () use ($importer) {
                $importer();

                return true;
            });
        }

        $this->newLine();
        $this->table(['Entity', 'Rows'], collect($this->counts)
            ->map(fn ($count, $entity) => [$entity, number_format($count)])
            ->values()->all());

        if ($this->problems !== []) {
            $this->newLine();
            $this->error(count($this->problems) . ' problem(s) found:');
            foreach (array_slice($this->problems, 0, 25) as $problem) {
                $this->line('  • ' . $problem);
            }
            if (count($this->problems) > 25) {
                $this->line('  … and ' . (count($this->problems) - 25) . ' more.');
            }

            return self::FAILURE;
        }

        $this->newLine();
        $this->info($this->dryRun ? 'Dry run clean — safe to import.' : 'Import complete.');

        return self::SUCCESS;
    }

    /* ------------------------------------------------------------ sections */

    private function importReference(): void
    {
        // Delivery modes are a fixed vocabulary, not derived from the data.
        foreach ([
            ['Online', 'online', 'monitor', 'Live instructor-led virtual classrooms, maximum 12 participants.'],
            ['Classroom', 'classroom', 'building', 'Scheduled public courses in 26 European cities, maximum 14 participants.'],
            ['Onsite', 'onsite', 'users', 'Delivered at your premises anywhere in Europe.'],
            ['Self-paced', 'self-paced', 'play', 'Video modules, exercises and knowledge checks, 12 months access.'],
        ] as $i => [$name, $slug, $icon, $description]) {
            $this->write(fn () => DeliveryMode::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'icon' => $icon, 'description' => $description, 'sort_order' => $i]
            ));
        }
        $this->counts['delivery_modes'] = 4;

        $rows = $this->readCsv('locations.csv');
        $countries = 0;
        $cities = 0;
        $venues = 0;

        foreach ($rows as $row) {
            $countryName = trim($row['country'] ?? '');
            $cityName    = trim($row['city'] ?? '');

            if ($countryName === '' || $cityName === '') {
                $this->problem("locations.csv: row missing city or country");
                continue;
            }

            $country = $this->write(fn () => Country::updateOrCreate(
                ['iso2' => strtoupper(trim($row['country_code'] ?: substr($countryName, 0, 2)))],
                [
                    'name'     => $countryName,
                    'slug'     => Str::slug($countryName),
                    'currency' => 'EUR',
                ]
            ));
            $countries++;

            $city = $this->write(fn () => City::updateOrCreate(
                [
                    'country_id' => $country?->id ?? 0,
                    'slug'       => Str::slug($cityName),
                ],
                [
                    'name'        => $cityName,
                    'intro'       => $row['intro'] ?: null,
                    'description' => $row['description'] ?: null,
                    'latitude'    => is_numeric($row['lat'] ?? null) ? (float) $row['lat'] : null,
                    'longitude'   => is_numeric($row['lng'] ?? null) ? (float) $row['lng'] : null,
                    'is_active'   => true,
                ]
            ));
            $cities++;

            if (($row['venue_name'] ?? '') !== '') {
                $this->write(fn () => Venue::updateOrCreate(
                    ['city_id' => $city?->id ?? 0, 'name' => $row['venue_name']],
                    [
                        'address_line1'   => Str::before($row['address'] ?? '', "\n") ?: null,
                        'transport_notes' => $row['transport'] ?: null,
                        'capacity'        => 14,
                        'is_active'       => true,
                    ]
                ));
                $venues++;
            }
        }

        $this->counts['countries'] = $countries;
        $this->counts['cities']    = $cities;
        $this->counts['venues']    = $venues;

        $this->importTaxonomy();
    }

    /**
     * Categories and subcategories are derived from the course rows.
     *
     * The 12-category structure from the specification is applied as a
     * mapping over the source subcategories, so the taxonomy the site
     * presents is decoupled from how the data happened to be authored.
     */
    private function importTaxonomy(): void
    {
        $map  = config('academia_taxonomy.map', []);
        $rows = $this->readCsv('courses.csv');

        $seen = [];
        foreach ($rows as $row) {
            $sub = trim($row['subcategory'] ?? '');
            if ($sub === '') {
                continue;
            }
            $seen[$sub] = ($seen[$sub] ?? 0) + 1;
        }

        $order = 0;
        foreach ($map as $categoryName => $definition) {
            $category = $this->write(fn () => CourseCategory::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name'        => $categoryName,
                    'summary'     => $definition['summary'] ?? null,
                    'icon'        => $definition['icon'] ?? null,
                    'color_token' => $definition['color'] ?? null,
                    'sort_order'  => $order++,
                    'is_active'   => true,
                ]
            ));

            $subOrder = 0;
            foreach ($definition['subcategories'] ?? [] as $subName) {
                $this->write(fn () => CourseSubcategory::updateOrCreate(
                    [
                        'course_category_id' => $category?->id ?? 0,
                        'slug'               => Str::slug($subName),
                    ],
                    ['name' => $subName, 'sort_order' => $subOrder++, 'is_active' => true]
                ));
                unset($seen[$subName]);
            }
        }

        // Anything in the data that the taxonomy does not account for is a
        // real problem: it would silently vanish from every category page.
        foreach ($seen as $orphan => $count) {
            $this->problem("Subcategory '{$orphan}' ({$count} courses) is not mapped to any category.");
        }

        $this->counts['categories']    = count($map);
        $this->counts['subcategories'] = collect($map)->sum(fn ($d) => count($d['subcategories'] ?? []));
    }

    private function importTrainers(): void
    {
        $rows  = $this->readCsv('trainers.csv');
        $count = 0;

        foreach ($rows as $row) {
            $city = City::where('name', trim($row['base_city'] ?? ''))->first();

            $this->write(fn () => Trainer::updateOrCreate(
                ['reference' => $row['trainer_ref']],
                [
                    'name'             => $row['name'],
                    'slug'             => Str::slug($row['name']),
                    'headline'         => $row['role'] ?: null,
                    'bio_short'        => $row['bio'] ?: null,
                    'bio_full'         => $row['bio_long'] ?: null,
                    'years_experience' => (int) ($row['years'] ?? 0) ?: null,
                    'certifications'   => $this->splitList($row['certifications'] ?? '', ','),
                    'languages'        => $this->splitList($row['languages'] ?? '', ','),
                    'linkedin_url'     => $row['linkedin'] ?: null,
                    'city_id'          => $city?->id,
                    'days_delivered'   => (int) ($row['days_delivered'] ?? 0),
                    // ---------------------------------------------------------
                    // NOT PUBLIC. See spec §3.1 — trainer visibility is an open
                    // business decision, and the safe default is the one that
                    // cannot leak. Flip is_public per trainer, deliberately.
                    // ---------------------------------------------------------
                    'is_public'        => false,
                    'published_at'     => null,
                    'status'           => 'active',
                ]
            ));
            $count++;
        }

        $this->counts['trainers'] = $count;
    }

    private function importCourses(): void
    {
        $rows          = $this->readCsv('courses.csv');
        $subcategories = CourseSubcategory::pluck('id', 'name');
        $modes         = DeliveryMode::pluck('id', 'name');
        $trainers      = Trainer::pluck('id', 'reference');
        $schemes       = [];
        $count         = 0;
        $moduleCount   = 0;

        foreach ($rows as $row) {
            $subName = trim($row['subcategory'] ?? '');
            $subId   = $subcategories[$subName] ?? null;

            if ($subId === null) {
                $this->problem("Course {$row['course_code']}: unknown subcategory '{$subName}'.");
                continue;
            }

            // Certification scheme, created on first sighting.
            $schemeId = null;
            if (($row['scheme_name'] ?? '') !== '') {
                $key = $row['scheme_name'];

                if (! isset($schemes[$key])) {
                    $scheme = $this->write(fn () => CertificationScheme::updateOrCreate(
                        ['slug' => Str::slug($key)],
                        [
                            'name'           => $key,
                            'owner'          => $row['scheme_owner'] ?: 'the scheme owner',
                            'status'         => SchemeStatus::tryFrom($row['scheme_status'] ?? '')
                                                ?? SchemeStatus::Independent,
                            'match_needle'   => $key,
                            'exam_questions' => $row['exam_questions'] ?: null,
                            'exam_format'    => $row['exam_format'] ?: null,
                            'exam_pass_mark' => $row['exam_pass_mark'] ?: null,
                            'exam_duration'  => $row['exam_duration'] ?: null,
                            'exam_book'      => $row['exam_book'] ?: null,
                            'pathway'        => $row['certification_pathway'] ?: null,
                        ]
                    ));
                    $schemes[$key] = $scheme?->id;
                }

                $schemeId = $schemes[$key];
            }

            $level = CourseLevel::tryFrom(strtolower(trim($row['level'] ?? '')));

            if ($level === null) {
                $this->problem("Course {$row['course_code']}: unknown level '{$row['level']}'.");
                continue;
            }

            $course = $this->write(fn () => Course::updateOrCreate(
                ['code' => $row['course_code']],
                [
                    'course_subcategory_id'   => $subId,
                    'certification_scheme_id' => $schemeId,
                    'title'                   => $row['title'],
                    'slug'                    => $row['slug'] ?: Str::slug($row['title']),
                    'summary'                 => Str::limit($row['summary'] ?? '', 310),
                    'description'             => $row['description'] ?? '',
                    'learning_objectives'     => $this->splitList($row['objectives'] ?? ''),
                    'target_audience'         => $row['audience'] ?: null,
                    'prerequisites'           => $row['prerequisites'] ?: null,
                    'includes'                => $this->splitList($row['includes'] ?? ''),
                    'duration_days'           => (float) ($row['days'] ?? 1),
                    'duration_hours'          => (int) ($row['hours'] ?? 0) ?: null,
                    'level'                   => $level,
                    'max_participants'        => 14,
                    'price_cents'             => $this->toCents($row['price'] ?? null),
                    'self_paced_price_cents'  => ($row['self_paced'] ?? '') === 'yes'
                                                    ? $this->toCents($row['self_paced_price'] ?? null)
                                                    : null,
                    'day_rate_cents'          => $this->toCents($row['day_rate'] ?? null),
                    'currency'                => 'EUR',
                    'certificate'             => $row['certificate'] ?: null,
                    'status'                  => CourseStatus::Published,
                    'published_at'            => now(),
                ]
            ));

            if ($course === null) {
                $count++;
                continue;
            }

            // Delivery modes
            $modeIds = collect($this->splitList($row['delivery_modes'] ?? ''))
                ->map(fn ($name) => $modes[$name] ?? null)
                ->filter()->values()->all();

            if ($modeIds !== []) {
                $course->deliveryModes()->sync($modeIds);
            }

            // Trainer (internal association; visibility governed separately)
            if (($ref = $row['trainer_ref'] ?? '') !== '' && isset($trainers[$ref])) {
                $course->trainers()->syncWithoutDetaching([
                    $trainers[$ref] => ['is_lead' => true],
                ]);
            }

            // Syllabus modules — "Title::bullet;bullet" pipe-delimited.
            $modules = $this->parseSyllabus($row['syllabus'] ?? '');

            if ($modules !== []) {
                $course->modules()->delete();

                foreach ($modules as $i => $module) {
                    CourseModule::create([
                        'course_id'  => $course->id,
                        'title'      => $module['title'],
                        'bullets'    => $module['bullets'],
                        'sort_order' => $i,
                    ]);
                    $moduleCount++;
                }
            }

            $count++;
        }

        $this->counts['courses']        = $count;
        $this->counts['course_modules'] = $moduleCount;
        $this->counts['schemes']        = count($schemes);
    }

    private function importSessions(): void
    {
        $rows    = $this->readCsv('sessions.csv');
        $courses = Course::pluck('id', 'code');
        $cities  = City::with('country')->get()->keyBy('name');
        $modes   = DeliveryMode::pluck('id', 'name');
        $venues  = Venue::pluck('id', 'name');
        $count   = 0;

        foreach ($rows as $row) {
            $courseId = $courses[$row['course_code']] ?? null;

            if ($courseId === null) {
                $this->problem("Session {$row['session_ref']}: unknown course {$row['course_code']}.");
                continue;
            }

            $modeName = trim($row['mode'] ?? '');
            $modeId   = $modes[$modeName] ?? null;

            if ($modeId === null) {
                $this->problem("Session {$row['session_ref']}: unknown delivery mode '{$modeName}'.");
                continue;
            }

            // Online and onsite sessions legitimately have no city. A
            // *classroom* session without one is a data error, because it
            // would never appear on any city page.
            $cityName = trim($row['city'] ?? '');
            $city     = $cityName !== '' ? ($cities[$cityName] ?? null) : null;

            if ($cityName !== '' && $city === null) {
                $this->problem("Session {$row['session_ref']}: unknown city '{$cityName}'.");
                continue;
            }

            if ($modeName === 'Classroom' && $city === null) {
                $this->problem("Session {$row['session_ref']}: classroom session with no city.");
                continue;
            }

            $timezone = $city?->country?->timezone ?? 'Europe/Amsterdam';
            $time     = $row['start_time'] ?: '09:00';

            // Stored UTC; the IANA zone is kept alongside so display can
            // convert. A session shown in the wrong zone is a refund.
            $startsAt = Carbon::parse("{$row['start_date']} {$time}", $timezone)->utc();
            $endsAt   = Carbon::parse("{$row['end_date']} 17:00", $timezone)->utc();

            if ($endsAt->lte($startsAt)) {
                $endsAt = $startsAt->copy()->addHours(8);
            }

            $seatLimit = (int) ($row['seats_total'] ?? 12);
            $seatsLeft = (int) ($row['seats_left'] ?? 0);
            $taken     = max(0, min($seatLimit, $seatLimit - $seatsLeft));

            $status = match (strtolower($row['status'] ?? '')) {
                'full'      => ScheduleStatus::Full,
                'cancelled' => ScheduleStatus::Cancelled,
                'completed' => ScheduleStatus::Completed,
                default     => ScheduleStatus::Open,
            };

            $this->write(fn () => CourseSchedule::updateOrCreate(
                ['reference' => $row['session_ref']],
                [
                    'course_id'        => $courseId,
                    'delivery_mode_id' => $modeId,
                    'country_id'       => $city?->country_id,
                    'city_id'          => $city?->id,
                    'venue_id'         => $venues[$row['venue'] ?? ''] ?? null,
                    'starts_at'        => $startsAt,
                    'ends_at'          => $endsAt,
                    'timezone'         => $timezone,
                    'seat_limit'       => $seatLimit,
                    'seats_taken'      => $taken,
                    'price_cents'      => $this->toCents($row['price'] ?? null),
                    'currency'         => 'EUR',
                    'status'           => $status,
                    'language'         => 'en',
                ]
            ));

            $count++;
        }

        $this->counts['sessions'] = $count;

        if (! $this->dryRun) {
            $this->refreshNextSessionAt();
        }
    }

    private function importGlossary(): void
    {
        $file = $this->path . '/glossary.csv';

        if (! file_exists($file)) {
            return;
        }

        $rows          = $this->readCsv('glossary.csv');
        $subcategories = CourseSubcategory::pluck('id', 'name');
        $count         = 0;

        foreach ($rows as $row) {
            $term = $row['term'] ?? $row['title'] ?? null;

            if (! $term) {
                continue;
            }

            $this->write(fn () => GlossaryTerm::updateOrCreate(
                ['slug' => $row['slug'] ?: Str::slug(Str::before($term, ' ('))],
                [
                    'term'                  => $term,
                    'definition'            => $row['definition'] ?? $row['short'] ?? '',
                    'body'                  => $row['body'] ?? null,
                    'course_subcategory_id' => $subcategories[$row['subcategory'] ?? ''] ?? null,
                    'is_active'             => true,
                ]
            ));
            $count++;
        }

        $this->counts['glossary_terms'] = $count;
    }

    /**
     * Rebuild the denormalised courses.next_session_at column.
     *
     * One grouped query rather than 524 per-course lookups. Also runs nightly
     * as a safety net behind the model observer.
     */
    private function refreshNextSessionAt(): void
    {
        DB::table('courses')->update(['next_session_at' => null]);

        $soonest = DB::table('course_schedules')
            ->select('course_id', DB::raw('MIN(starts_at) as next_start'))
            ->where('status', ScheduleStatus::Open->value)
            ->where('starts_at', '>', now())
            ->whereNull('deleted_at')
            ->groupBy('course_id')
            ->get();

        foreach ($soonest->chunk(200) as $chunk) {
            foreach ($chunk as $record) {
                DB::table('courses')
                    ->where('id', $record->course_id)
                    ->update(['next_session_at' => $record->next_start]);
            }
        }
    }

    /* ------------------------------------------------------------- helpers */

    /** @return array<int, array<string, string>> */
    private function readCsv(string $filename): array
    {
        $file = $this->path . '/' . $filename;

        if (! file_exists($file)) {
            $this->problem("Missing file: {$filename}");

            return [];
        }

        $handle  = fopen($file, 'r');
        $headers = fgetcsv($handle);
        $rows    = [];

        while (($data = fgetcsv($handle)) !== false) {
            if ($data === [null] || $data === []) {
                continue;
            }

            $rows[] = array_combine(
                $headers,
                array_pad(array_slice($data, 0, count($headers)), count($headers), '')
            );
        }

        fclose($handle);

        return $rows;
    }

    /** Pipe-delimited list to a clean array. */
    private function splitList(string $value, string $delimiter = '|'): array
    {
        return collect(explode($delimiter, $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Parse the syllabus field into ordered modules.
     *
     * Format, as authored in the source CSV:
     *   "Module title::bullet;bullet;bullet<SECTION>Next module::bullet;bullet"
     *
     * Modules are separated by the section sign, bullets by semicolons, and
     * the title is split from its bullets by a double colon. Note the module
     * separator is NOT the pipe used elsewhere in this file — pipe appears
     * inside prose bullets, which is exactly why a different character was
     * chosen for the outer delimiter.
     */
    private function parseSyllabus(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        return collect(explode(self::MODULE_SEPARATOR, $value))
            ->map(function (string $chunk): ?array {
                [$title, $bullets] = array_pad(explode('::', $chunk, 2), 2, '');

                if (trim($title) === '') {
                    return null;
                }

                return [
                    'title'   => trim($title),
                    'bullets' => $this->splitList($bullets, ';'),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /** Money always becomes an integer number of cents. */
    private function toCents(mixed $value): ?int
    {
        if ($value === null || $value === '' || ! is_numeric($value)) {
            return null;
        }

        return (int) round(((float) $value) * 100);
    }

    /** Executes a write unless this is a dry run. */
    private function write(callable $operation): mixed
    {
        return $this->dryRun ? null : $operation();
    }

    private function problem(string $message): void
    {
        $this->problems[] = $message;
    }
}
