<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    // Hashed automatically by the model's 'hashed' cast. Only
                    // send it when set, so editing a user without retyping the
                    // password leaves it unchanged.
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText('Leave blank to keep the current password.'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('job_title'),
                Select::make('trainer_id')
                    ->label('Linked trainer')
                    ->relationship('trainer', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->disabled(fn (): bool => ! Auth::user()?->can('assign_roles'))
                    ->dehydrated(fn (): bool => (bool) Auth::user()?->can('assign_roles')),
                TextInput::make('locale')
                    ->required()
                    ->default('en'),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
