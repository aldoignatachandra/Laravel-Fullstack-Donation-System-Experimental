<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_category_id',
        'user_id',
        'image',
        'title',
        'description',
        'slug',
        'target_amount',
        'start_date',
        'end_date',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'donation_count',
        'total_donations',
    ];

    const STATUS_DRAFT = 0;

    const STATUS_ACTIVE = 1;

    const STATUS_PAUSED = 2;

    const STATUS_COMPLETED = 3;

    const STATUS_CANCELLED = 4;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the category that owns the campaign.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CampaignCategory::class, 'campaign_category_id');
    }

    /**
     * Get the user that owns the campaign.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the donations for the campaign.
     */
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Updates/articles related to the campaign.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(CampaignArticle::class);
    }

    /**
     * Get the donation count for the campaign.
     */
    public function getDonationCountAttribute(): int
    {
        return $this->donations()->count();
    }

    /**
     * Get the total donations amount for the campaign.
     */
    public function getTotalDonationsAttribute(): float
    {
        return $this->donations()->where('status', 1)->sum('amount');
    }
}
