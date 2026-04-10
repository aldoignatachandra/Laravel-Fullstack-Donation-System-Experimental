<?php

namespace App\Filament\Resources\Campaigns\Resources\Donations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
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
                Hidden::make('campaign_id'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('payment_method')
                    ->required(),
                Select::make('status')
                    ->options([
                        0 => 'Pending',
                        1 => 'Paid',
                        2 => 'Failed',
                        3 => 'Cancelled',
                    ])
                    ->required()
                    ->default(0),
                Toggle::make('is_anonymous')
                    ->required(),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('order_id')
                    ->required(),
                Select::make('payment_type')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'e_wallet' => 'E-Wallet',
                        'cash' => 'Cash',
                    ])
                    ->required(),
                DateTimePicker::make('paid_at'),
            ]);
    }
}
