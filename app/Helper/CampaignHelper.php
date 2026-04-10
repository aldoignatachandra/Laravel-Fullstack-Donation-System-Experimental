<?php

namespace App\Helper;

class CampaignHelper
{
    /**
     * Get status text based on campaign status
     */
    public static function getStatusText(int $status): string
    {
        return match ($status) {
            0 => 'Draft',
            1 => 'Aktif',
            2 => 'Dijeda',
            3 => 'Selesai',
            4 => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    /**
     * Get CSS class for status badge
     */
    public static function getStatusClass(int $status): string
    {
        return match ($status) {
            0 => 'bg-gray-100 text-gray-800',
            1 => 'bg-green-100 text-green-800',
            2 => 'bg-yellow-100 text-yellow-800',
            3 => 'bg-blue-100 text-blue-800',
            4 => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status icon class
     */
    public static function getStatusIcon(int $status): string
    {
        return match ($status) {
            0 => 'fas fa-edit',
            1 => 'fas fa-play-circle',
            2 => 'fas fa-pause-circle',
            3 => 'fas fa-check-circle',
            4 => 'fas fa-times-circle',
            default => 'fas fa-circle'
        };
    }

    /**
     * Get progress percentage class based on status
     */
    public static function getProgressClass(int $status): string
    {
        return match ($status) {
            0 => 'bg-gray-300',
            1 => 'bg-green-500',
            2 => 'bg-yellow-500',
            3 => 'bg-blue-500',
            4 => 'bg-red-500',
            default => 'bg-gray-300'
        };
    }

    /**
     * Get status with icon and class
     */
    public static function getStatusBadge(int $status): array
    {
        return [
            'text' => self::getStatusText($status),
            'class' => self::getStatusClass($status),
            'icon' => self::getStatusIcon($status),
        ];
    }

    /**
     * Check if campaign is active
     */
    public static function isActive(int $status): bool
    {
        return $status === 1;
    }

    /**
     * Check if campaign is completed
     */
    public static function isCompleted(int $status): bool
    {
        return $status === 3;
    }

    /**
     * Check if campaign is draft
     */
    public static function isDraft(int $status): bool
    {
        return $status === 0;
    }

    /**
     * Check if campaign is paused
     */
    public static function isPaused(int $status): bool
    {
        return $status === 2;
    }

    /**
     * Check if campaign is cancelled
     */
    public static function isCancelled(int $status): bool
    {
        return $status === 4;
    }

    /**
     * Calculate progress percentage
     */
    public static function getProgressPercent(float $totalDonations, float $targetAmount): float
    {
        if ($targetAmount <= 0) {
            return 0;
        }

        return min(100, ($totalDonations / $targetAmount) * 100);
    }

    /**
     * Calculate days left for campaign
     */
    public static function getDaysLeft(?\DateTime $endDate): int
    {
        if (! $endDate) {
            return 0;
        }

        return max(0, now()->diffInDays($endDate, false));
    }

    /**
     * Get progress data for campaign
     */
    public static function getProgressData($campaign): array
    {
        return [
            'percent' => self::getProgressPercent($campaign->total_donations, $campaign->target_amount),
            'days_left' => self::getDaysLeft($campaign->end_date),
            'progress_class' => self::getProgressClass($campaign->status),
            'formatted_percent' => number_format(self::getProgressPercent($campaign->total_donations, $campaign->target_amount), 1),
        ];
    }

    /**
     * Get image URL for campaign
     */
    public static function getImageUrl(?string $image): string
    {
        return $image ? asset('storage/'.$image) : asset('images/indonesia.jpg');
    }
}
