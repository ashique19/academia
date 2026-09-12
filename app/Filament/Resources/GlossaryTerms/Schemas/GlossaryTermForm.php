<?php

namespace App\Filament\Resources\GlossaryTerms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GlossaryTermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('term')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('definition')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->columnSpanFull(),
                TextInput::make('course_subcategory_id')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
