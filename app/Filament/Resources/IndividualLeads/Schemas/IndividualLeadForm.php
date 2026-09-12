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
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->dehydrated(fn (string $operation): bool => $operation === 'create')
                    ->visible(fn (string $operation): bool => $operation === 'edit'),
                TextInput::make('name'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Select::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload(),
                Select::make('course_schedule_id')
                    ->label('Schedule')
                    ->relationship(
                        name: 'schedule',
                        titleAttribute: 'reference',
                        modifyQueryUsing: fn ($query) => $query->orderByDesc('starts_at'),
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => trim(($record->course?->title ? $record->course->title.' — ' : '').$record->reference))
                    ->searchable()
                    ->preload(),
                DatePicker::make('preferred_date'),
                Textarea::make('message')
                    ->columnSpanFull(),
                Select::make('source')
                    ->options(LeadSource::class)
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('new'),
                Select::make('assigned_to')
                    ->label('Assignee')
                    ->relationship(
                        name: 'assignee',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn ($query) => $query->staff(),
                    )
                    ->searchable()
                    ->preload(),
                TextInput::make('utm_source'),
                TextInput::make('utm_medium'),
                TextInput::make('utm_campaign'),
                DateTimePicker::make('consented_at')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('consent_ip')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('consent_version')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('confirmed_at'),
            ]);
    }
}
