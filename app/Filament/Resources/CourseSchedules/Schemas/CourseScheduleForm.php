<?php

namespace App\Filament\Resources\CourseSchedules\Schemas;

use App\Domain\Scheduling\Enums\ScheduleStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('delivery_mode_id')
                    ->relationship('deliveryMode', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('venue_id')
                    ->relationship('venue', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('trainer_id')
                    ->relationship('trainer', 'name')
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->required(),
                TextInput::make('timezone')
                    ->required()
                    ->default('Europe/Amsterdam'),
                TextInput::make('seat_limit')
                    ->required()
                    ->numeric(),
                TextInput::make('seats_taken')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Updated automatically by bookings.'),
                TextInput::make('price_cents')
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                Select::make('status')
                    ->options(ScheduleStatus::class)
                    ->default(ScheduleStatus::Scheduled)
                    ->required()
                    ->disabled()
                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Use header actions to publish or cancel.'),
                TextInput::make('language')
                    ->required()
                    ->default('en'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
