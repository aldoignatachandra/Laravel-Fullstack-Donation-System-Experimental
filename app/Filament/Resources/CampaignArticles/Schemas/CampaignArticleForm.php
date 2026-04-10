<?php

namespace App\Filament\Resources\CampaignArticles\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CampaignArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                ->columnSpanFull()
                    ->description('Complete the main details of the campaign article below.')
                    ->icon('heroicon-o-information-circle')
                    ->columns(2)
                    ->schema([
                        Select::make('campaign_id')
                            ->label('Select Campaign')
                            ->placeholder('Search campaign...')
                            ->searchable()
                            ->preload()
                            ->relationship('campaign', 'title')
                            ->required()
                            ->columnSpan(1),
                        Select::make('author_id')
                            ->label('Author')
                            ->placeholder('Select author...')
                            ->searchable()
                            ->preload()
                            ->relationship('author', 'name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('title')
                            ->label('Article Title')
                            ->placeholder('Enter article title')
                            ->required()
                            ->columnSpan(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                // Generate slug from title
                                $slug = Str::slug($state);
                                $set('slug', $slug);
                            }),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->placeholder('Slug will be automatically generated from title')
                            ->required()
                            ->columnSpan(2)
                            ->helperText('Slug will be automatically generated from the article title. You can edit it if needed.'),
                        RichEditor::make('content')
                            ->label('Content')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike', 'link', 'orderedList', 'bulletList', 'blockquote', 'codeBlock', 'undo', 'redo'
                            ])
                            ->placeholder('Write article content here...')
                            ->required()
                            ->columnSpan(2)
                            ->extraAttributes(['style' => 'min-height: 300px;']),
                    ])
            ]);
    }
}
