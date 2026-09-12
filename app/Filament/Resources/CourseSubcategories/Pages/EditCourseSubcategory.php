<?php

namespace App\Filament\Resources\CourseSubcategories\Pages;

use App\Filament\Resources\CourseSubcategories\CourseSubcategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseSubcategory extends EditRecord
{
    protected static string $resource = CourseSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
