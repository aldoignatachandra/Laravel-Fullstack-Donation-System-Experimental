<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'amount',
        'payment_method',
        'status',
        'is_anonymous',
        'message',
        'order_id',
        'payment_type',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'paid_at' => 'datetime'
    ];

    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_FAILED = 2;
    const STATUS_CANCELLED = 3;
    const PAYMENT_METHOD = 'midtrans';

    /**
     * Get the campaign that owns the donation.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the user that owns the donation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
