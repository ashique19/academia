<?php

namespace App\Filament\Resources\IndividualLeads\Schemas;

use App\Domain\Leads\Enums\LeadSource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class IndividualLeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Select::make('country_id')
                    ->relationship('country', 'name'),
                Select::make('city_id')
                    ->relationship('city', 'name'),
                Select::make('course_id')
                    ->relationship('course', 'title'),
                TextInput::make('course_schedule_id')
                    ->numeric(),
                DatePicker::make('preferred_date'),
                Textarea::make('message')
                    ->columnSpanFull(),
                Select::make('source')
                    ->options(LeadSource::class)
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                TextInput::make('assigned_to')
                    ->numeric(),
                TextInput::make('utm_source'),
                TextInput::make('utm_medium'),
                TextInput::make('utm_campaign'),
                DateTimePicker::make('consented_at'),
                TextInput::make('consent_ip'),
                TextInput::make('consent_version'),
                DateTimePicker::make('confirmed_at'),
            ]);
    }
}
