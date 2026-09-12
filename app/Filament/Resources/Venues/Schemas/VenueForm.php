<?php

namespace App\Filament\Resources\Venues\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VenueForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('address_line1'),
                TextInput::make('address_line2'),
                TextInput::make('postcode'),
                Textarea::make('transport_notes')
                    ->columnSpanFull(),
                Textarea::make('facilities')
                    ->columnSpanFull(),
                Textarea::make('accessibility_notes')
                    ->columnSpanFull(),
                TextInput::make('capacity')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
