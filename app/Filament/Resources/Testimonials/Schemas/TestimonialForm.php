<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('author_name'),
                TextInput::make('author_role'),
                TextInput::make('author_sector'),
                TextInput::make('organisation'),
                Textarea::make('quote')
                    ->required()
                    ->columnSpanFull(),
                Select::make('course_id')
                    ->relationship('course', 'title'),
                TextInput::make('delivery_mode'),
                TextInput::make('rating')
                    ->numeric(),
                Toggle::make('is_verified')
                    ->required(),
                TextInput::make('consent_reference'),
                Toggle::make('is_illustrative')
                    ->required(),
                TextInput::make('verified_by')
                    ->numeric(),
                DateTimePicker::make('verified_at'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
