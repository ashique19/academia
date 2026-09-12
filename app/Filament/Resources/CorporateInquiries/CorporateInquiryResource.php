<?php

namespace App\Filament\Resources\CorporateInquiries;

use App\Domain\Leads\Models\CorporateInquiry;
use App\Filament\Resources\CorporateInquiries\Pages\CreateCorporateInquiry;
use App\Filament\Resources\CorporateInquiries\Pages\EditCorporateInquiry;
use App\Filament\Resources\CorporateInquiries\Pages\ListCorporateInquiries;
use App\Filament\Resources\CorporateInquiries\RelationManagers\NotesRelationManager;
use App\Filament\Resources\CorporateInquiries\RelationManagers\StatusChangesRelationManager;
use App\Filament\Resources\CorporateInquiries\Schemas\CorporateInquiryForm;
use App\Filament\Resources\CorporateInquiries\Tables\CorporateInquiriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorporateInquiryResource extends Resource
{
    protected static ?string $model = CorporateInquiry::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Leads';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CorporateInquiryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CorporateInquiriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            NotesRelationManager::class,
            StatusChangesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCorporateInquiries::route('/'),
            'create' => CreateCorporateInquiry::route('/create'),
            'edit' => EditCorporateInquiry::route('/{record}/edit'),
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
