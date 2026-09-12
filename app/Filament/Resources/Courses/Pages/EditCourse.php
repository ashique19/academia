<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Domain\Catalogue\Enums\CourseStatus;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->authorize('publish')
                ->visible(fn (): bool => $this->getRecord()->status !== CourseStatus::Published)
                ->requiresConfirmation()
                ->action(function (): void {
                    $course = $this->getRecord();
                    $course->update([
                        'status' => CourseStatus::Published,
                        'published_at' => $course->published_at ?? now(),
                    ]);

                    Notification::make()
                        ->title('Course published')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
