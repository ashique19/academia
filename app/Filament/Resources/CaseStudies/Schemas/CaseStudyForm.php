<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('sector'),
                TextInput::make('summary'),
                Textarea::make('background')
                    ->columnSpanFull(),
                Textarea::make('approach_points')
                    ->columnSpanFull(),
                TextInput::make('client_name'),
                Textarea::make('client_quote')
                    ->columnSpanFull(),
                TextInput::make('client_quote_attribution'),
                Toggle::make('client_approved')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
            ]);
    }
}
