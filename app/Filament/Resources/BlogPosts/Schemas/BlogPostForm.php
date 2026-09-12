<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Filament\Support\SeoFormSection;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('blog_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('author_id')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('excerpt'),
                Textarea::make('body')
                    ->columnSpanFull(),
                TextInput::make('reading_minutes')
                    ->numeric(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('published_at'),
                SeoFormSection::make(),
            ]);
    }
}
