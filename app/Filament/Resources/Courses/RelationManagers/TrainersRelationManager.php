<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainersRelationManager extends RelationManager
{
    protected static string $relationship = 'trainers';

    protected static ?string $title = 'Trainers';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('is_lead')
                ->label('Lead trainer')
                ->default(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('headline')
                    ->toggleable()
                    ->limit(40),
                IconColumn::make('pivot.is_lead')
                    ->label('Lead')
                    ->boolean(),
                IconColumn::make('is_public')
                    ->boolean(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Toggle::make('is_lead')
                            ->label('Lead trainer')
                            ->default(false),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->toolbarActions([
                DetachBulkAction::make(),
            ]);
    }
}
