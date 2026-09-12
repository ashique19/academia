<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('iso2')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                TextInput::make('vat_rate')
                    ->numeric(),
                TextInput::make('timezone')
                    ->required()
                    ->default('Europe/Amsterdam'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
