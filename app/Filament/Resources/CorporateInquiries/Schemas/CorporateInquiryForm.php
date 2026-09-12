<?php

namespace App\Filament\Resources\CorporateInquiries\Schemas;

use App\Domain\Leads\Enums\LeadStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CorporateInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('sector'),
                TextInput::make('company_size'),
                Select::make('country_id')
                    ->relationship('country', 'name'),
                Select::make('city_id')
                    ->relationship('city', 'name'),
                TextInput::make('contact_name')
                    ->required(),
                TextInput::make('job_title'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('participants')
                    ->required()
                    ->numeric(),
                Select::make('delivery_mode_id')
                    ->relationship('deliveryMode', 'name'),
                Select::make('course_id')
                    ->relationship('course', 'title'),
                TextInput::make('topic'),
                DatePicker::make('preferred_start_date'),
                TextInput::make('preferred_window'),
                TextInput::make('budget_range'),
                Textarea::make('message')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(LeadStatus::class)
                    ->default('new')
                    ->required(),
                TextInput::make('assigned_to')
                    ->numeric(),
                TextInput::make('estimated_value_cents')
                    ->numeric(),
                TextInput::make('won_value_cents')
                    ->numeric(),
                TextInput::make('lost_reason'),
                DateTimePicker::make('first_response_due_at'),
                DateTimePicker::make('first_responded_at'),
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
