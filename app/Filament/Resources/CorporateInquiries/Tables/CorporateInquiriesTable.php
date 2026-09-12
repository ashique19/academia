<?php

namespace App\Filament\Resources\CorporateInquiries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CorporateInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                TextColumn::make('company_name')
                    ->searchable(),
                TextColumn::make('sector')
                    ->searchable(),
                TextColumn::make('company_size')
                    ->searchable(),
                TextColumn::make('country.name')
                    ->searchable(),
                TextColumn::make('city.name')
                    ->searchable(),
                TextColumn::make('contact_name')
                    ->searchable(),
                TextColumn::make('job_title')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('participants')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deliveryMode.name')
                    ->searchable(),
                TextColumn::make('course.title')
                    ->searchable(),
                TextColumn::make('topic')
                    ->searchable(),
                TextColumn::make('preferred_start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('preferred_window')
                    ->searchable(),
                TextColumn::make('budget_range')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('assigned_to')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('estimated_value_cents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('won_value_cents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('lost_reason')
                    ->searchable(),
                TextColumn::make('first_response_due_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('first_responded_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('utm_source')
                    ->searchable(),
                TextColumn::make('utm_medium')
                    ->searchable(),
                TextColumn::make('utm_campaign')
                    ->searchable(),
                TextColumn::make('consented_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('consent_ip')
                    ->searchable(),
                TextColumn::make('consent_version')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
