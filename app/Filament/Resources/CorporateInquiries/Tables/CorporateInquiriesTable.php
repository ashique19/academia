<?php

namespace App\Filament\Resources\CorporateInquiries\Tables;

use App\Domain\Leads\Enums\LeadStatus;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CorporateInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact_name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assignee')
                    ->placeholder('Unassigned')
                    ->sortable(),
                TextColumn::make('first_response_due_at')
                    ->label('SLA due')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : null),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(LeadStatus::class),
                SelectFilter::make('assigned_to')
                    ->label('Assignee')
                    ->options(fn (): array => User::query()->staff()->orderBy('name')->pluck('name', 'id')->all()),
                Filter::make('overdue_sla')
                    ->label('Overdue SLA')
                    ->query(fn (Builder $query): Builder => $query->overdueSla()),
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
