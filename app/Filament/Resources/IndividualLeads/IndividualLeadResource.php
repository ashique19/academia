<?php

namespace App\Filament\Resources\IndividualLeads;

use App\Domain\Leads\Models\IndividualLead;
use App\Filament\Resources\IndividualLeads\Pages\CreateIndividualLead;
use App\Filament\Resources\IndividualLeads\Pages\EditIndividualLead;
use App\Filament\Resources\IndividualLeads\Pages\ListIndividualLeads;
use App\Filament\Resources\IndividualLeads\Schemas\IndividualLeadForm;
use App\Filament\Resources\IndividualLeads\Tables\IndividualLeadsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IndividualLeadResource extends Resource
{
    protected static ?string $model = IndividualLead::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Leads';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return IndividualLeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndividualLeadsTable::configure($table);
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
            'index' => ListIndividualLeads::route('/'),
            'create' => CreateIndividualLead::route('/create'),
            'edit' => EditIndividualLead::route('/{record}/edit'),
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
