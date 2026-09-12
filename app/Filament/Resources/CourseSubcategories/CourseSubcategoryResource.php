<?php

namespace App\Filament\Resources\CourseSubcategories;

use App\Domain\Catalogue\Models\CourseSubcategory;
use App\Filament\Resources\CourseSubcategories\Pages\CreateCourseSubcategory;
use App\Filament\Resources\CourseSubcategories\Pages\EditCourseSubcategory;
use App\Filament\Resources\CourseSubcategories\Pages\ListCourseSubcategories;
use App\Filament\Resources\CourseSubcategories\Schemas\CourseSubcategoryForm;
use App\Filament\Resources\CourseSubcategories\Tables\CourseSubcategoriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseSubcategoryResource extends Resource
{
    protected static ?string $model = CourseSubcategory::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Catalogue';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CourseSubcategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseSubcategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseSubcategories::route('/'),
            'create' => CreateCourseSubcategory::route('/create'),
            'edit' => EditCourseSubcategory::route('/{record}/edit'),
        ];
    }
}
