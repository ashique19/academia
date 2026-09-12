<?php

namespace App\Filament\Resources\DeliveryModes;

use App\Domain\Catalogue\Models\DeliveryMode;
use App\Filament\Resources\DeliveryModes\Pages\CreateDeliveryMode;
use App\Filament\Resources\DeliveryModes\Pages\EditDeliveryMode;
use App\Filament\Resources\DeliveryModes\Pages\ListDeliveryModes;
use App\Filament\Resources\DeliveryModes\Schemas\DeliveryModeForm;
use App\Filament\Resources\DeliveryModes\Tables\DeliveryModesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DeliveryModeResource extends Resource
{
    protected static ?string $model = DeliveryMode::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogue';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DeliveryModeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeliveryModesTable::configure($table);
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
            'index' => ListDeliveryModes::route('/'),
            'create' => CreateDeliveryMode::route('/create'),
            'edit' => EditDeliveryMode::route('/{record}/edit'),
        ];
    }
}
