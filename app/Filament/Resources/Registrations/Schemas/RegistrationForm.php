<?php

namespace App\Filament\Resources\Registrations\Schemas;

use App\Domain\Leads\Enums\RegistrationStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('course_schedule_id')
                    ->required()
                    ->numeric(),
                Select::make('user_id')
                    ->relationship('user', 'name'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('company'),
                TextInput::make('job_title'),
                Select::make('country_id')
                    ->relationship('country', 'name'),
                Textarea::make('message')
                    ->columnSpanFull(),
                Textarea::make('dietary_requirements')
                    ->columnSpanFull(),
                TextInput::make('seats')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options(RegistrationStatus::class)
                    ->default('interest')
                    ->required(),
                TextInput::make('price_paid_cents')
                    ->numeric(),
                TextInput::make('discount_code'),
                TextInput::make('discount_percent')
                    ->numeric(),
                TextInput::make('source'),
                TextInput::make('utm_source'),
                TextInput::make('utm_medium'),
                TextInput::make('utm_campaign'),
                DateTimePicker::make('consented_at'),
                TextInput::make('consent_ip'),
                TextInput::make('consent_version'),
            ]);
    }
}
