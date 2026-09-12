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
                    ->required(),
                Select::make('delivery_mode_id')
                    ->relationship('deliveryMode', 'name')
                    ->required(),
                Select::make('country_id')
                    ->relationship('country', 'name'),
                Select::make('city_id')
                    ->relationship('city', 'name'),
                Select::make('venue_id')
                    ->relationship('venue', 'name'),
                Select::make('trainer_id')
                    ->relationship('trainer', 'name'),
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
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('price_cents')
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                Select::make('status')
                    ->options(ScheduleStatus::class)
                    ->default('scheduled')
                    ->required(),
                TextInput::make('language')
                    ->required()
                    ->default('en'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
