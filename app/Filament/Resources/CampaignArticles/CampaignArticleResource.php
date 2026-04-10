<?php

namespace App\Filament\Resources\CampaignArticles;

use App\Filament\Resources\CampaignArticles\Pages\CreateCampaignArticle;
use App\Filament\Resources\CampaignArticles\Pages\EditCampaignArticle;
use App\Filament\Resources\CampaignArticles\Pages\ListCampaignArticles;
use App\Filament\Resources\CampaignArticles\Schemas\CampaignArticleForm;
use App\Filament\Resources\CampaignArticles\Tables\CampaignArticlesTable;
use App\Models\CampaignArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CampaignArticleResource extends Resource
{
    protected static ?string $model = CampaignArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentText;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationLabel = 'Campaign Articles';

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Management';

    public static function form(Schema $schema): Schema
    {
        return CampaignArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignArticlesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaignArticles::route('/'),
            'create' => CreateCampaignArticle::route('/create'),
            'edit' => EditCampaignArticle::route('/{record}/edit'),
        ];
    }
}
