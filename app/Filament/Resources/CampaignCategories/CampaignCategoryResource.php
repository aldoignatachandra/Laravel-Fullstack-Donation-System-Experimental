<?php

namespace App\Filament\Resources\CampaignCategories;

use App\Filament\Resources\CampaignCategories\Pages\CreateCampaignCategory;
use App\Filament\Resources\CampaignCategories\Pages\EditCampaignCategory;
use App\Filament\Resources\CampaignCategories\Pages\ListCampaignCategories;
use App\Filament\Resources\CampaignCategories\Pages\ViewCampaignCategory;
use App\Filament\Resources\CampaignCategories\Schemas\CampaignCategoryForm;
use App\Filament\Resources\CampaignCategories\Schemas\CampaignCategoryInfolist;
use App\Filament\Resources\CampaignCategories\Tables\CampaignCategoriesTable;
use App\Models\CampaignCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CampaignCategoryResource extends Resource
{
    protected static ?string $model = CampaignCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BarsArrowUp;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Campaign Management';

    protected static ?string $navigationLabel = 'Campaign Categories';

    public static function form(Schema $schema): Schema
    {
        return CampaignCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CampaignCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignCategoriesTable::configure($table);
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
            'index' => ListCampaignCategories::route('/'),
            'create' => CreateCampaignCategory::route('/create'),
            'view' => ViewCampaignCategory::route('/{record}'),
            'edit' => EditCampaignCategory::route('/{record}/edit'),
        ];
    }
}
