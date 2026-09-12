<?php

namespace App\Filament\Resources\CourseSchedules\Pages;

use App\Filament\Resources\CourseSchedules\CourseScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseSchedule extends EditRecord
{
    protected static string $resource = CourseScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
