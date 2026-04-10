<?php

namespace App\Filament\Resources\Campaigns\Resources\Donations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DonationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Donatur'),
                TextEntry::make('amount')
                    ->label('Jumlah Donasi')
                    ->money('IDR'),
                TextEntry::make('payment_method')
                    ->label('Metode Pembayaran'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'gray',
                        '1' => 'success',
                        '2' => 'danger',
                        '3' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '0' => 'Pending',
                        '1' => 'Paid',
                        '2' => 'Failed',
                        '3' => 'Cancelled',
                        default => 'Unknown',
                    }),
                IconEntry::make('is_anonymous')
                    ->label('Anonim')
                    ->boolean(),
                TextEntry::make('message')
                    ->label('Pesan')
                    ->columnSpanFull(),
                TextEntry::make('order_id')
                    ->label('Order ID'),
                TextEntry::make('payment_type')
                    ->label('Tipe Pembayaran')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'e_wallet' => 'E-Wallet',
                        'cash' => 'Cash',
                        default => $state,
                    }),
                TextEntry::make('paid_at')
                    ->label('Tanggal Dibayar')
                    ->dateTime('d M Y H:i'),
                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i'),
            ]);
    }
}
