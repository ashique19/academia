<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course_subcategory_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('certification_scheme_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('summary')
                    ->searchable(),
                TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('duration_hours')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('level')
                    ->badge()
                    ->searchable(),
                TextColumn::make('max_participants')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_cents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('prior_price_cents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('self_paced_price_cents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('day_rate_cents')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('certificate')
                    ->searchable(),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('next_session_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('booking_count')
                    ->numeric()
                    ->sortable(),
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
