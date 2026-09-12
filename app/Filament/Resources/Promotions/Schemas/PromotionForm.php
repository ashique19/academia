<?php

namespace App\Filament\Resources\Promotions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PromotionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('percentage')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->required()
                    ->default('campaign'),
                Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->required(),
                TextInput::make('blurb'),
                Textarea::make('applies_to_category_slugs')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('max_uses')
                    ->numeric(),
                TextInput::make('uses_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
