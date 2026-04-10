<?php

namespace App\Filament\Resources\Donations\Helpers;

use App\Models\Donation;

class DonationHelper
{
    public static function getDonationStatusLabel(int $status)
    {
        return match ($status) {
            Donation::STATUS_PENDING => 'Pending',
            Donation::STATUS_PAID => 'Paid',
            Donation::STATUS_FAILED => 'Failed',
            Donation::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown',
        };
    }

    public static function getDonationStatusColor(int $status)
    {
        return match ($status) {
            Donation::STATUS_PENDING => 'warning',
            Donation::STATUS_PAID => 'success',
            Donation::STATUS_FAILED => 'danger',
            Donation::STATUS_CANCELLED => 'secondary',
            default => 'gray',
        };
    }
}
