<?php

namespace App\Filament\Resources\Donations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DonationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('campaign_id')
                    ->numeric(),
                TextEntry::make('user_id')
                    ->numeric(),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('payment_method'),
                TextEntry::make('status')
                    ->numeric(),
                IconEntry::make('is_anonymous')
                    ->boolean(),
                TextEntry::make('order_id'),
                TextEntry::make('payment_type'),
                TextEntry::make('paid_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
