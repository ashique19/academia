<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

/**
 * Shared SEO morphOne editor for Course, City and BlogPost forms.
 */
final class SeoFormSection
{
    public static function make(): Section
    {
        return Section::make('SEO')
            ->description('Overrides the public meta tags for this record. Leave blank to fall back to generated defaults.')
            ->relationship('seo')
            ->collapsed()
            ->schema([
                TextInput::make('title')
                    ->label('Meta title')
                    ->maxLength(60)
                    ->helperText('Aim for ≤ 60 characters.'),
                Textarea::make('description')
                    ->label('Meta description')
                    ->rows(3)
                    ->maxLength(160)
                    ->helperText('Aim for ≤ 160 characters.'),
                TextInput::make('keywords')
                    ->maxLength(320),
                TextInput::make('og_title')
                    ->label('Open Graph title')
                    ->maxLength(60),
                Textarea::make('og_description')
                    ->label('Open Graph description')
                    ->rows(2)
                    ->maxLength(160),
                FileUpload::make('og_image_path')
                    ->label('Open Graph image')
                    ->image()
                    ->directory('seo')
                    ->visibility('public'),
                TextInput::make('canonical_url')
                    ->label('Canonical URL')
                    ->url()
                    ->maxLength(255),
                TextInput::make('robots')
                    ->default('index,follow')
                    ->maxLength(60),
            ])
            ->columns(2);
    }
}
