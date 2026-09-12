<?php

declare(strict_types=1);

namespace App\Filament\Resources\CorporateInquiries\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusChangesRelationManager extends RelationManager
{
    protected static string $relationship = 'statusChanges';

    protected static ?string $title = 'Status history';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_status')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('to_status')
                    ->badge(),
                TextColumn::make('note')
                    ->wrap()
                    ->limit(80)
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
