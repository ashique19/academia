<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Domain\Catalogue\Enums\CourseLevel;
use App\Domain\Catalogue\Enums\CourseStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('course_subcategory_id')
                    ->required()
                    ->numeric(),
                TextInput::make('certification_scheme_id')
                    ->numeric(),
                TextInput::make('code')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('summary')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('learning_objectives')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('target_audience')
                    ->columnSpanFull(),
                Textarea::make('prerequisites')
                    ->columnSpanFull(),
                Textarea::make('includes')
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
                    ->numeric(),
                TextInput::make('prior_price_cents')
                    ->numeric(),
                TextInput::make('self_paced_price_cents')
                    ->numeric(),
                TextInput::make('day_rate_cents')
                    ->numeric(),
                TextInput::make('currency')
                    ->required()
                    ->default('EUR'),
                TextInput::make('certificate'),
                Textarea::make('certification_note')
                    ->columnSpanFull(),
                Toggle::make('is_featured')
                    ->required(),
                Select::make('status')
                    ->options(CourseStatus::class)
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at'),
                DateTimePicker::make('next_session_at'),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('booking_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
