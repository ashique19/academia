<?php

declare(strict_types=1);

use App\Domain\Scheduling\Services\ScheduleStateService;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled maintenance
|--------------------------------------------------------------------------
|
| Three of these exist because the alternative is a site that decays without
| anyone noticing: sessions that never complete, a schedule nobody refreshes,
| and a promotion reference price recorded after the campaign already started.
|
*/

// Past sessions become Completed and drop out of every public query.
// Without this, the schedule silently fills with dates that have been and gone.
Schedule::call(fn (ScheduleStateService $state) => $state->completePastSessions())
    ->dailyAt('02:00')
    ->name('sessions:complete-past')
    ->onOneServer();

// Rebuild the denormalised next_session_at column. The observer keeps it
// correct in normal operation; this is the safety net behind it.
Schedule::command('academia:refresh-next-sessions')
    ->dailyAt('02:15')
    ->onOneServer();

// EU Omnibus reference price, recorded BEFORE a campaign begins so the
// evidence exists rather than being reconstructed afterwards.
Schedule::command('academia:record-prior-prices')
    ->dailyAt('02:30')
    ->onOneServer();

// Staleness alerting. An out-of-date schedule is the worst trust signal in
// this category — worse than no schedule at all.
Schedule::command('academia:schedule-alerts')
    ->weekdays()
    ->dailyAt('07:00')
    ->onOneServer();

Schedule::command('academia:validate')
    ->dailyAt('03:00')
    ->onOneServer();

// Data-retention enforcement (spec §22.1). The privacy policy promises that
// unconverted enquiries are anonymised after 24 months and booking PII after
// the 7-year statutory window; this is what actually delivers it.
Schedule::command('academia:purge-expired-leads')
    ->dailyAt('03:30')
    ->onOneServer();
