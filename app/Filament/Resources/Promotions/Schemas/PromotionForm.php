<?php

namespace App\Filament\Resources\Promotions\Schemas;

use App\Domain\Catalogue\Models\CourseCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                Select::make('type')
                    ->options([
                        'campaign' => 'Campaign',
                        'seasonal' => 'Seasonal',
                        'partner' => 'Partner',
                        'clearance' => 'Clearance',
                    ])
                    ->required()
                    ->default('campaign'),
                Textarea::make('reason')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->required()
                    ->after('starts_at'),
                TextInput::make('blurb'),
                Select::make('applies_to_category_slugs')
                    ->label('Applies to categories')
                    ->multiple()
                    ->options(fn (): array => CourseCategory::query()->orderBy('name')->pluck('name', 'slug')->all())
                    ->searchable()
                    ->preload(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('max_uses')
                    ->numeric(),
                TextInput::make('uses_count')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Incremented automatically when the code is used.'),
            ]);
    }
}
