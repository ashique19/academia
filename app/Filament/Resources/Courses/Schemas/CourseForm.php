<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Domain\Catalogue\Enums\CourseLevel;
use App\Domain\Catalogue\Enums\CourseStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_subcategory_id')
                    ->label('Subcategory')
                    ->relationship('subcategory', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('certification_scheme_id')
                    ->label('Certification scheme')
                    ->relationship('scheme', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('summary')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('learning_objectives')
                    ->helperText('One objective per line.')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                    ->dehydrateStateUsing(fn ($state) => self::toList($state))
                    ->columnSpanFull(),
                Textarea::make('target_audience')
                    ->columnSpanFull(),
                Textarea::make('prerequisites')
                    ->columnSpanFull(),
                Textarea::make('includes')
                    ->helperText('One item per line.')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                    ->dehydrateStateUsing(fn ($state) => self::toList($state))
                    ->columnSpanFull(),
                TextInput::make('duration_days')
                    ->required()
                    ->numeric(),
                TextInput::make('duration_hours')
                    ->numeric(),
                Select::make('level')
                    ->options(CourseLevel::class)
                    ->required(),
                TextInput::make('max_participants')
                    ->required()
                    ->numeric()
                    ->default(14),
                TextInput::make('price_cents')
                    ->label('List price (cents)')
                    ->numeric(),
                TextInput::make('prior_price_cents')
                    ->label('Prior price (cents)')
                    ->numeric(),
                TextInput::make('self_paced_price_cents')
                    ->label('Self-paced price (cents)')
                    ->numeric(),
                TextInput::make('day_rate_cents')
                    ->label('In-company day rate (cents)')
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                TextInput::make('certificate'),
                Textarea::make('certification_note')
                    ->columnSpanFull(),
                Toggle::make('is_featured'),
                Select::make('status')
                    ->options(CourseStatus::class)
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at'),
            ]);
    }

    /** Split newline-separated textarea input into a clean list for array casts. */
    private static function toList(mixed $state): array
    {
        if (is_array($state)) {
            return array_values($state);
        }

        return array_values(array_filter(array_map('trim', explode("\n", (string) $state))));
    }
}
