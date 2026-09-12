<?php

namespace App\Filament\Resources\CertificationSchemes\Schemas;

use App\Domain\Catalogue\Enums\SchemeStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CertificationSchemeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('owner')
                    ->required(),
                Select::make('status')
                    ->options(SchemeStatus::class)
                    ->default('independent')
                    ->required(),
                TextInput::make('match_needle')
                    ->required(),
                TextInput::make('exam_questions'),
                TextInput::make('exam_format'),
                TextInput::make('exam_pass_mark'),
                TextInput::make('exam_duration'),
                TextInput::make('exam_book'),
                Textarea::make('pathway')
                    ->columnSpanFull(),
            ]);
    }
}
