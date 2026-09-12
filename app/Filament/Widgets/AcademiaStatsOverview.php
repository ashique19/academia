<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Domain\Catalogue\Enums\CourseStatus;
use App\Domain\Catalogue\Models\Course;
use App\Domain\Leads\Enums\LeadStatus;
use App\Domain\Leads\Models\CorporateInquiry;
use App\Domain\Scheduling\Models\CourseSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AcademiaStatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && (
            $user->can('view_any_report')
            || $user->can('view_any_corporate_inquiry')
            || $user->hasRole('super-admin')
        );
    }

    protected function getStats(): array
    {
        $published = Course::query()->where('status', CourseStatus::Published)->count();
        $upcoming = CourseSchedule::query()->where('starts_at', '>', now())->count();
        $openInquiries = CorporateInquiry::query()
            ->whereNotIn('status', [LeadStatus::Won, LeadStatus::Lost, LeadStatus::Closed])
            ->count();
        $overdueSla = CorporateInquiry::query()->overdueSla()->count();

        return [
            Stat::make('Published courses', number_format($published))
                ->description('Live in the catalogue')
                ->color('success'),

            Stat::make('Upcoming sessions', number_format($upcoming))
                ->description('Scheduled after today')
                ->color('primary'),

            Stat::make('Open corporate inquiries', number_format($openInquiries))
                ->description('Awaiting a sales outcome')
                ->color($openInquiries > 0 ? 'warning' : 'gray'),

            Stat::make('Overdue SLA', number_format($overdueSla))
                ->description('First response past due')
                ->color($overdueSla > 0 ? 'danger' : 'success'),
        ];
    }
}
