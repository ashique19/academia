<?php

namespace App\Filament\Resources\CorporateInquiries\Schemas;

use App\Domain\Leads\Enums\LeadStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CorporateInquiryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Company')
                    ->columns(2)
                    ->schema([
                        TextInput::make('uuid')
                            ->label('UUID')
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
                            ->visible(fn (string $operation): bool => $operation === 'edit')
                            ->helperText('Assigned automatically on create.'),
                        TextInput::make('company_name')
                            ->required(),
                        TextInput::make('sector'),
                        TextInput::make('company_size'),
                        Select::make('country_id')
                            ->relationship('country', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        TextInput::make('contact_name')
                            ->required(),
                        TextInput::make('job_title'),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->tel(),
                    ]),

                Section::make('Training need')
                    ->columns(2)
                    ->schema([
                        TextInput::make('participants')
                            ->required()
                            ->numeric(),
                        Select::make('delivery_mode_id')
                            ->relationship('deliveryMode', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('course_id')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->preload(),
                        TextInput::make('topic'),
                        DatePicker::make('preferred_start_date'),
                        TextInput::make('preferred_window'),
                        TextInput::make('budget_range'),
                        Textarea::make('message')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pipeline')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->options(LeadStatus::class)
                            ->default(LeadStatus::New)
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation): ?string => $operation === 'edit'
                                ? 'Use header actions to change status.'
                                : null),
                        Select::make('assigned_to')
                            ->label('Assignee')
                            ->relationship(
                                name: 'assignee',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->staff(),
                            )
                            ->searchable()
                            ->preload()
                            ->optionsLimit(50),
                        TextInput::make('estimated_value_cents')
                            ->numeric()
                            ->label('Estimated value (cents)'),
                        TextInput::make('won_value_cents')
                            ->numeric()
                            ->label('Won value (cents)')
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),
                        TextInput::make('lost_reason')
                            ->disabled(fn (string $operation): bool => $operation === 'edit')
                            ->dehydrated(false),
                        DateTimePicker::make('first_response_due_at'),
                        DateTimePicker::make('first_responded_at')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('source'),
                        TextInput::make('utm_source'),
                        TextInput::make('utm_medium'),
                        TextInput::make('utm_campaign'),
                    ]),

                Section::make('Consent')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('consented_at')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('consent_ip')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('consent_version')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
