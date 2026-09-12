<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected static ?string $title = 'Syllabus modules';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(200),
            Textarea::make('body')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('bullets')
                ->helperText('One bullet per line.')
                ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode("\n", (string) $state)))))
                ->columnSpanFull(),
            TextInput::make('duration_hours')
                ->numeric()
                ->step(0.5),
            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('duration_hours')
                    ->label('Hours'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
