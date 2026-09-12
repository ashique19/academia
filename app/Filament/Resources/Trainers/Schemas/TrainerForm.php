<?php

namespace App\Filament\Resources\Trainers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('headline'),
                Textarea::make('bio_short')
                    ->columnSpanFull(),
                Textarea::make('bio_full')
                    ->columnSpanFull(),
                TextInput::make('years_experience')
                    ->numeric(),
                Textarea::make('certifications')
                    ->helperText('One certification per line.')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                    ->dehydrateStateUsing(fn ($state) => self::toList($state))
                    ->columnSpanFull(),
                Textarea::make('languages')
                    ->helperText('One language per line.')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                    ->dehydrateStateUsing(fn ($state) => self::toList($state))
                    ->columnSpanFull(),
                TextInput::make('linkedin_url')
                    ->url(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                Toggle::make('is_public')
                    ->label('Visible on public /trainers')
                    ->required(),
                DateTimePicker::make('published_at'),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('day_rate_cents')
                    ->numeric(),
                TextInput::make('contract_type'),
                Textarea::make('availability_notes')
                    ->columnSpanFull(),
                DatePicker::make('reference_checked_at'),
                TextInput::make('internal_rating')
                    ->numeric(),
                TextInput::make('days_delivered')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    /** @return list<string> */
    private static function toList(mixed $state): array
    {
        if (is_array($state)) {
            return array_values($state);
        }

        return array_values(array_filter(array_map('trim', explode("\n", (string) $state))));
    }
}
