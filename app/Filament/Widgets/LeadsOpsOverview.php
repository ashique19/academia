<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Leads\Models\IndividualLead;
use App\Domain\Scheduling\Models\CourseSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LeadsOpsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return $user->can('view_any_report')
            || $user->can('view_any_corporate_inquiry')
            || $user->can('view_any_individual_lead')
            || $user->hasRole('super-admin');
    }

    protected function getStats(): array
    {
        $unassigned = CorporateInquiry::query()->open()->unassigned()->count();
        $newToday = IndividualLead::query()
            ->whereDate('created_at', today())
            ->count();
        $upcomingWeek = CourseSchedule::query()
            ->whereBetween('starts_at', [now(), now()->addDays(7)])
            ->count();

        return [
            Stat::make('Unassigned open inquiries', number_format($unassigned))
                ->description('Need an owner')
                ->color($unassigned > 0 ? 'warning' : 'success'),

            Stat::make('Individual leads today', number_format($newToday))
                ->description('Created since midnight')
                ->color('primary'),

            Stat::make('Sessions next 7 days', number_format($upcomingWeek))
                ->description('Upcoming deliveries')
                ->color('info'),
        ];
    }
}
