<?php

namespace App\Filament\Resources\CourseSubcategories\Pages;

use App\Filament\Resources\CourseSubcategories\CourseSubcategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseSubcategories extends ListRecords
{
    protected static string $resource = CourseSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
