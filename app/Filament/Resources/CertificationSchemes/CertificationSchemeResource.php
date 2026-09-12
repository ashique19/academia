<?php

namespace App\Filament\Resources\CertificationSchemes;

use App\Domain\Catalogue\Models\CertificationScheme;
use App\Filament\Resources\CertificationSchemes\Pages\CreateCertificationScheme;
use App\Filament\Resources\CertificationSchemes\Pages\EditCertificationScheme;
use App\Filament\Resources\CertificationSchemes\Pages\ListCertificationSchemes;
use App\Filament\Resources\CertificationSchemes\Schemas\CertificationSchemeForm;
use App\Filament\Resources\CertificationSchemes\Tables\CertificationSchemesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CertificationSchemeResource extends Resource
{
    protected static ?string $model = CertificationScheme::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CertificationSchemeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificationSchemesTable::configure($table);
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
            'index' => ListCertificationSchemes::route('/'),
            'create' => CreateCertificationScheme::route('/create'),
            'edit' => EditCertificationScheme::route('/{record}/edit'),
        ];
    }
}
