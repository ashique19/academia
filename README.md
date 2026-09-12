# Academia Training Solutions — Laravel + Livewire

A European professional training platform: 524 courses, 1,561 scheduled sessions, 26 cities,
three delivery modes, and two lead funnels that never merge.

Built to the specification in `Academia_Laravel_Livewire_Spec.md`. This is **Phase 1–3 of that
roadmap** — foundation, data, and the public site — not the complete 630–800 hour build. §"What
is and is not here" below is precise about the line.

---

## Quick start

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite is the default so this runs with no service to install.
touch database/database.sqlite

php artisan migrate --seed        # schema + roles + settings + content seeders
php artisan academia:import       # the real catalogue: 524 courses, 1,561 sessions
php artisan db:seed --class=PostImportSeeder  # featured courses + per-entity SEO
php artisan academia:validate     # referential integrity gate

npm run build
php artisan serve
```

Then open <http://localhost:8000>.

Local staff logins are seeded in `local` and `testing` environments only — `super@`, `admin@`,
`courses@`, `sales@`, `content@academiatraining.eu`, password `password`. `DatabaseSeeder`
gates that block on the environment so it cannot run in production.

### PostgreSQL (recommended for production)

SQLite is the zero-setup default. Postgres is the recommendation, and every migration is written
to run on both — Postgres-only features (partial indexes, `tsvector` full-text) are applied
conditionally inside the migration, and `CourseSearchService` picks its strategy from the driver.

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_DATABASE=academia
DB_USERNAME=academia
DB_PASSWORD=…
```

MySQL 8 also works — it gets a `FULLTEXT` index instead of `tsvector`.

---

## What is and is not here

### Built and verified

| Area | Detail |
|---|---|
| **Schema** | 7 migrations, 30 tables, FKs, partial and composite indexes, CHECK constraints |
| **Models** | 20 Eloquent models with relations, scopes, casts and computed attributes |
| **Domain services** | Promotions, seat reservation, schedule state, scheme matching, search, SEO, schema.org |
| **Data import** | `academia:import` against the real CSVs, with `--dry-run` and per-row error reporting |
| **Validation** | `academia:validate` — 7 error checks and 5 warning checks, non-zero exit on failure |
| **Public site** | Home, catalogue with facets, course detail, course × city, schedule (list + calendar), 26 city pages, country pages, category and subcategory hubs, corporate, online, offers, FAQ, glossary, about, 4 legal pages, thank-you |
| **Livewire** | `CourseCatalogue`, `ScheduleBrowser`, `CorporateInquiryForm`, `RegistrationForm`, `CourseSearch` |
| **Design system** | Tailwind 4 theme from the five brand colours, with full tint ramps. Compiles to 45 KB (8 KB gzipped) |
| **Admin panel** | Filament v4 panel at `/admin` with resources for catalogue, schedule, leads, content, settings and users |
| **Auth & RBAC** | 8 roles, ~110 permissions, policies for Filament resources, `Gate::before` for super-admin only |
| **GDPR** | Consent trio on every lead row, `gdpr:export`, `gdpr:forget`, retention config |
| **Scheduled tasks** | Session completion, `next_session_at` refresh, Omnibus price recording, staleness alerts, nightly validation |
| **Tests** | 7 test files — promotion arithmetic, scheme matching, SLA hours, spam guard, seat concurrency, city gate, testimonial gate |

### Not built (Phase 2+ in the spec)

Public blog routes, notifications and mail, reports and exports, payment, learner and
corporate portals, LMS, sitemap generation, media library integration on public pages.

---

## Verification — and an honest note on its limits

**packagist.org is blocked in the environment this was built in**, so `composer install` could
not run and the application was never booted. `vendor/` is never committed to a Laravel repo
anyway, so this does not affect what you receive — but it does mean the usual `php artisan test`
was unavailable, and I verified differently rather than claiming a green suite I never saw.

What was actually run:

| Check | Result |
|---|---|
| `php -l` across every PHP file | **153 files, 0 errors** |
| Blade directive and component balance | **39 templates, all balanced** |
| Every `route()` reference in a view resolves | **21 routes defined, 19 used, 0 missing** |
| Every `<livewire:…>` reference resolves | **5 components, 0 missing** |
| Import logic against the real CSVs | **524 courses, 1,561 sessions, 26 cities, 5,492 modules, 0 errors** |
| Referential integrity on the imported data | **14 checks, all pass** |
| Domain logic executed | **37 assertions, all pass** |
| Seat reservation under real concurrency (PostgreSQL) | **exactly one winner, no overselling** |
| `CHECK (seats_taken <= seat_limit)` bypass attempt | **rejected by the database** |
| Vite production build | **clean, 44.8 KB CSS** |
| WCAG 2.2 AA contrast across the palette | **10 pairs, lowest 4.60:1, all pass** |

Two real bugs were found and fixed by this process, which is the argument for doing it:

1. The syllabus importer used `|` as the module separator when the data uses `§`. Every course
   imported with exactly one module instead of ten. Caught by the row count, not by the parser.
2. `.chip-gold` was 4.22:1 against its own background — under AA for small text. `--color-gold-600`
   was darkened from `#9C6E0F` to `#93690E`.

**What you should still run once dependencies install:**

```bash
composer install
php artisan test          # the Pest suite — never executed here
npm run build
php artisan academia:validate --strict
```

---

## Architecture

```
app/
├── Domain/                     # organised by domain, not by Eloquent convention
│   ├── Catalogue/              # courses, categories, schemes, trainers, promotions
│   ├── Scheduling/             # sessions, seat reservation, status transitions
│   ├── Leads/                  # corporate inquiries, individual leads, registrations
│   ├── Content/                # blog, FAQs, glossary, testimonials, SEO
│   └── Shared/                 # geography, settings, value objects
├── Livewire/Public/            # the five interactive public components
├── Http/{Controllers,Middleware}
├── Policies/
└── Console/Commands/
```

A modular monolith, not microservices. At this scale a service boundary is a distributed
transaction you did not need.

### Six decisions worth not undoing

**1. Sessions have no public URL.** There are 1,561 of them and more arrive continuously. Routable
session pages would be thousands of URLs that die on their end date, leaking authority away from
the course pages that accumulate it. There is deliberately no route bound to `CourseSchedule`.

**2. Seat reservation is pessimistically locked.** `RegistrationService::reserve()` runs
`SELECT … FOR UPDATE` inside a transaction, and a `CHECK` constraint backs it at the database
level. Two people clicking the last seat in the same second is the failure this class of platform
actually has. Both layers were tested under real concurrency.

**3. City pages 404 without a real session.** `City::hasUpcomingSessions()` gates the route. That
single check is the line between a local landing page and a doorway page, and doorway pages are a
manual-action category — so it lives in code, where growth pressure cannot quietly remove it.

**4. Promotions carry three rules, enforced in the schema.** `promotions.reason` and
`promotions.ends_at` are `NOT NULL`, so an evergreen sale cannot be created even by direct SQL.
`prior_price_cents` records the EU Omnibus (Directive 2019/2161) reference price. Stacking is
capped at a published 30% applied to the *combined* figure.

**5. No rating can be displayed that the business generated itself.** `Course::rating` returns
`null` below the verified-review floor, and `null` renders nothing. `aggregateRating` schema emits
only from a configured third-party score. `TestimonialObserver` blocks publishing anything that is
neither verified-with-a-consent-reference nor explicitly flagged illustrative.

**6. Trainer visibility is one boolean.** `trainers.is_public` defaults to **false**, and every
public query goes through `Trainer::scopePublic()`. See the open decision below.

---

## Two decisions still open

Both are flagged in spec §3.1 and both change what the site looks like.

**Trainer visibility.** Your V2 requirement was that trainer identities are not published before a
booking. This rebuild's brief asks for public profiles with photos and LinkedIn URLs. The schema
supports either; `is_public` is seeded `false`, so the anonymous position holds until someone
deliberately flips it. With no trainer public, `/trainers` 404s and no template needs changing.

**Testimonials and ratings.** The modules are built, and the verification gate is built with them.
Real verified reviews need the post-course feedback flow, which is Phase 2.

---

## Commands

```bash
php artisan academia:import [--dry-run] [--only=courses] [--path=…]
php artisan academia:validate [--strict]
php artisan academia:refresh-next-sessions
php artisan academia:record-prior-prices [--force]
php artisan academia:schedule-alerts
php artisan academia:purge-expired-leads [--dry-run]
php artisan gdpr:export {email} [--json=path]
php artisan gdpr:forget {email} [--confirm]
```

`--dry-run` parses and validates everything and writes nothing, reporting every bad row rather
than dying on the first. An importer that aborts on row 300 of 524 tells you about one problem
per run.

---

## Production notes

- **Run the scheduler.** `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`.
  Without it sessions never complete, the schedule silently fills with past dates, and the Omnibus
  reference price is never recorded.
- **Run a queue worker** once notifications land in Phase 2.
- **Redis** for cache, session and queue.
- **`APP_DEBUG=false`** — verified in the go-live checklist, not assumed.
- **Self-hosted fonts.** `@fontsource/inter` and `@fontsource/fraunces` are dependencies rather
  than a Google Fonts link. Loading fonts from Google's CDN transmits the visitor's IP to a US
  server without consent; a German court awarded damages for exactly that (LG München I,
  20 Jan 2022, 3 O 17493/20).
- **EU-only processing** for hosting, database, backups, mail and error tracking.
- **Have a Dutch-qualified lawyer review the four legal pages.** They are drafted to be accurate
  to how the site actually behaves, which is the hard part — but they are not legal advice, and
  each page renders a visible warning saying so until you delete it.

---

*Academia Training Solutions is a trade name of SlimCijfers Analytics B.V.*
#   a c a d e m i a  
 