<?php

namespace App\Filament\Resources\CourseSchedules\Pages;

use App\Domain\Scheduling\Enums\ScheduleStatus;
use App\Domain\Scheduling\Exceptions\InvalidStatusTransitionException;
use App\Domain\Scheduling\Services\ScheduleStateService;
use App\Filament\Resources\CourseSchedules\CourseScheduleResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCourseSchedule extends EditRecord
{
    protected static string $resource = CourseScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->visible(fn (): bool => $this->getRecord()->status === ScheduleStatus::Scheduled)
                ->authorize('update')
                ->requiresConfirmation()
                ->action(function (ScheduleStateService $service): void {
                    try {
                        $service->publish($this->getRecord());
                    } catch (InvalidStatusTransitionException $exception) {
                        Notification::make()
                            ->title('Publish failed')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Session published')
                        ->success()
                        ->send();
                }),

            Action::make('cancel')
                ->label('Cancel session')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->authorize('cancel')
                ->visible(fn (): bool => in_array(
                    $this->getRecord()->status,
                    [ScheduleStatus::Scheduled, ScheduleStatus::Open, ScheduleStatus::Full],
                    true,
                ))
                ->form([
                    Textarea::make('reason')
                        ->label('Cancellation reason')
                        ->required()
                        ->rows(3)
                        ->helperText('Sent to every registrant.'),
                ])
                ->action(function (array $data, ScheduleStateService $service): void {
                    try {
                        $service->cancel($this->getRecord(), $data['reason']);
                    } catch (InvalidStatusTransitionException|\InvalidArgumentException $exception) {
                        Notification::make()
                            ->title('Cancel failed')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Session cancelled')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
