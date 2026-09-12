<?php

namespace App\Filament\Resources\CourseSchedules;

use App\Domain\Scheduling\Models\CourseSchedule;
use App\Filament\Resources\CourseSchedules\Pages\CreateCourseSchedule;
use App\Filament\Resources\CourseSchedules\Pages\EditCourseSchedule;
use App\Filament\Resources\CourseSchedules\Pages\ListCourseSchedules;
use App\Filament\Resources\CourseSchedules\Schemas\CourseScheduleForm;
use App\Filament\Resources\CourseSchedules\Tables\CourseSchedulesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseScheduleResource extends Resource
{
    protected static ?string $model = CourseSchedule::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Scheduling';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CourseScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseSchedulesTable::configure($table);
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
            'index' => ListCourseSchedules::route('/'),
            'create' => CreateCourseSchedule::route('/create'),
            'edit' => EditCourseSchedule::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
