<?php

namespace App\Filament\Resources\Donations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DonationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('campaign_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('payment_method')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_anonymous')
                    ->required(),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('order_id')
                    ->required(),
                TextInput::make('payment_type')
                    ->required(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
