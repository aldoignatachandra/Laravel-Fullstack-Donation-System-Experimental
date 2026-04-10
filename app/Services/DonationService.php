<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Donation;
use App\Notifications\DonationFailureNotification;
use App\Notifications\DonationSuccessNotification;
use App\Notifications\LargeDonationAlertNotification;
use App\Notifications\NewDonationReceivedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Midtrans\Config;
use Midtrans\Snap;

class DonationService
{
    // Business validation constants
    const MIN_DONATION_AMOUNT = 10000; // Rp 10,000

    const MAX_DONATION_AMOUNT = 100000000; // Rp 100,000,000

    const MAX_DAILY_DONATIONS_PER_USER = 100; // 100 donations

    const MAX_DAILY_AMOUNT_PER_USER = 5000000; // Rp 5,000,000

    public function __construct()
    {
        Config::$serverKey = config('payment.midtrans.server_key');
        Config::$isProduction = config('payment.midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Record a new donation with comprehensive business validation
     *
     * @return array
     *
     * @throws ValidationException
     * @throws \RuntimeException
     */
    public function recordDonation(Campaign $campaign, array $data)
    {
        // Pre-transaction validation
        $this->validateBusinessRules($campaign, $data);

        DB::beginTransaction();
        try {
            // Lock campaign for update to prevent race conditions
            // Lock will be automatically released when transaction ends (commit/rollback)
            $campaign = Campaign::query()
                ->where('id', '=', $campaign->id)
                ->lockForUpdate()->first();

            if (! $campaign) {
                throw new \RuntimeException('Campaign not found');
            }

            // Generate unique order ID
            $orderId = $this->generateOrderId();

            // Create donation record
            $donation = Donation::create([
                'campaign_id' => $campaign->id,
                'user_id' => Auth::id(),
                'amount' => $data['amount'],
                'payment_method' => Donation::PAYMENT_METHOD,
                'status' => Donation::STATUS_PENDING,
                'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
                'message' => $data['message'] ?? null,
                'order_id' => $orderId,
                'payment_type' => 'automatic',
                'paid_at' => null,
            ]);

            // Create payment link
            $snapLink = $this->createSnapLink($donation);

            // Log successful donation creation
            Log::info('Donation created successfully', [
                'donation_id' => $donation->id,
                'campaign_id' => $campaign->id,
                'user_id' => Auth::id(),
                'amount' => $data['amount'],
                'order_id' => $orderId,
            ]);

            DB::commit();

            return [
                'success' => true,
                'snap_url' => $snapLink,
                'donation_id' => $donation->id,
                'order_id' => $orderId,
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error with context
            Log::error('Failed to create donation', [
                'campaign_id' => $campaign->id,
                'user_id' => Auth::id(),
                'amount' => $data['amount'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \RuntimeException('Failed to create donation: '.$e->getMessage());
        }
    }

    /**
     * Validate business rules before creating donation
     *
     * @throws ValidationException
     */
    private function validateBusinessRules(Campaign $campaign, array $data)
    {
        $errors = [];

        // Validate campaign status
        if ($campaign->status !== Campaign::STATUS_ACTIVE) {
            $errors['campaign'] = 'Campaign is not active for donations';
        }

        // Check campaign dates
        if ($campaign->start_date && $campaign->start_date > now()) {
            $errors['campaign'] = 'Campaign has not started yet';
        }

        if ($campaign->end_date && $campaign->end_date < now()) {
            $errors['campaign'] = 'Campaign has ended';
        }

        // Validate donation amount
        $amount = $data['amount'] ?? 0;
        if (! is_numeric($amount) || $amount < self::MIN_DONATION_AMOUNT) {
            $errors['amount'] = 'Minimum donation amount is Rp '.number_format(self::MIN_DONATION_AMOUNT);
        }

        if ($amount > self::MAX_DONATION_AMOUNT) {
            $errors['amount'] = 'Maximum donation amount is Rp '.number_format(self::MAX_DONATION_AMOUNT);
        }

        // Validate user daily limits
        $today = now()->startOfDay();
        $userTodayDonations = Donation::query()
            ->where('user_id', '=', Auth::id())
            ->where('created_at', '>=', $today)
            ->count();

        if ($userTodayDonations >= self::MAX_DAILY_DONATIONS_PER_USER) {
            $errors['limit'] = 'Maximum daily donation limit reached';
        }

        $userTodayAmount = Donation::query()->where('user_id', '=', Auth::id())
            ->where('created_at', '>=', $today)
            ->where('status', Donation::STATUS_PAID)
            ->sum('amount');

        if (($userTodayAmount + $amount) > self::MAX_DAILY_AMOUNT_PER_USER) {
            $errors['limit'] = 'Daily donation amount limit exceeded';
        }

        // Check for duplicate donations (same amount within 5 minutes)
        $recentDonation = Donation::query()
            ->where('user_id', '=', Auth::id())
            ->where('campaign_id', $campaign->id)
            ->where('amount', $amount)
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();

        if ($recentDonation) {
            $errors['duplicate'] = 'Similar donation was made recently. Please wait before making another donation.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Generate unique order ID
     *
     * @return string
     */
    private function generateOrderId()
    {
        $orderId = 'ORD-'.now()->format('YmdHis').'-'.Str::random(6);

        return $orderId;
    }

    /**
     * Handle payment callback from Midtrans with comprehensive error handling and notifications
     *
     * @param  array  $payload
     * @return Donation|null
     *
     * @throws \Exception
     */
    public function handleCallback($payload)
    {
        DB::beginTransaction();
        try {
            // Validate payload
            if (empty($payload['order_id']) || empty($payload['transaction_status'])) {
                throw new \InvalidArgumentException('Invalid callback payload');
            }

            $transactionStatus = $payload['transaction_status'];
            $orderId = $payload['order_id'];

            // Find donation with lock to prevent race conditions
            // Lock will be automatically released when transaction ends (commit/rollback)
            $donation = Donation::query()->where('order_id', '=', $orderId)->lockForUpdate()->first();
            if (! $donation) {
                Log::error('Donation not found for callback', [
                    'order_id' => $orderId,
                    'payload' => $payload,
                ]);
                DB::rollBack();

                return null;
            }

            // Log callback received
            Log::info('Payment callback received', [
                'donation_id' => $donation->id,
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payload' => $payload,
            ]);

            $oldStatus = $donation->status;
            $wasSuccessful = false;

            // Update donation status based on transaction status
            switch ($transactionStatus) {
                case 'settlement':
                    $donation->status = Donation::STATUS_PAID;
                    $donation->paid_at = now();
                    $wasSuccessful = true;
                    break;
                case 'cancel':
                    $donation->status = Donation::STATUS_CANCELLED;
                    break;
                case 'deny':
                case 'expire':
                case 'failure':
                    $donation->status = Donation::STATUS_FAILED;
                    break;
                case 'pending':
                    $donation->status = Donation::STATUS_PENDING;
                    break;
                default:
                    Log::warning('Unknown transaction status received', [
                        'donation_id' => $donation->id,
                        'transaction_status' => $transactionStatus,
                    ]);
            }

            $donation->save();

            // Update campaign statistics if donation was successful
            if ($wasSuccessful && $oldStatus !== Donation::STATUS_PAID) {
                $this->updateCampaignStatistics($donation->campaign);
            }

            // Send notifications
            $this->sendDonationNotifications($donation, $wasSuccessful);

            DB::commit();

            Log::info('Payment callback processed successfully', [
                'donation_id' => $donation->id,
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $donation->status,
                'was_successful' => $wasSuccessful,
            ]);

            return $donation;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to process payment callback', [
                'order_id' => $payload['order_id'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);

            throw $e;
        }
    }

    /**
     * Create Snap payment link with error handling
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    private function createSnapLink(Donation $donation)
    {
        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $donation->order_id,
                    'gross_amount' => (int) $donation->amount,
                ],
                'customer_details' => [
                    'first_name' => $donation->user->name,
                    'email' => $donation->user->email,
                ],
                'item_details' => [
                    [
                        'id' => 'DONATION',
                        'price' => (int) $donation->amount,
                        'quantity' => 1,
                        'name' => 'Donation to '.$donation->campaign->title,
                    ],
                ],
                'callbacks' => [
                    'finish' => route('donation.payment'),
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'hour',
                    'duration' => 24, // 24 hours expiry
                ],
            ];

            Log::info('Creating Snap payment link', [
                'donation_id' => $donation->id,
                'order_id' => $donation->order_id,
                'amount' => $donation->amount,
            ]);

            $snapUrl = Snap::getSnapUrl($params);

            if (! $snapUrl) {
                throw new \RuntimeException('Failed to generate Snap payment URL');
            }

            Log::info('Snap payment link created successfully', [
                'donation_id' => $donation->id,
                'order_id' => $donation->order_id,
            ]);

            return $snapUrl;

        } catch (\Exception $e) {
            Log::error('Failed to create Snap payment link', [
                'donation_id' => $donation->id,
                'order_id' => $donation->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \RuntimeException('Failed to create payment link: '.$e->getMessage());
        }
    }

    /**
     * Update campaign statistics after successful donation
     */
    private function updateCampaignStatistics(Campaign $campaign)
    {
        try {
            // Calculate new total donations
            $totalDonations = $campaign->donations()->where('status', Donation::STATUS_PAID)->sum('amount');
            $donationCount = $campaign->donations()->where('status', Donation::STATUS_PAID)->count();

            // Check if campaign target is reached
            if ($totalDonations >= $campaign->target_amount && $campaign->status === Campaign::STATUS_ACTIVE) {
                $campaign->status = Campaign::STATUS_COMPLETED;
                $campaign->save();

                Log::info('Campaign target reached', [
                    'campaign_id' => $campaign->id,
                    'target_amount' => $campaign->target_amount,
                    'total_donations' => $totalDonations,
                ]);
            }

            Log::info('Campaign statistics updated', [
                'campaign_id' => $campaign->id,
                'total_donations' => $totalDonations,
                'donation_count' => $donationCount,
                'progress_percentage' => ($totalDonations / $campaign->target_amount) * 100,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update campaign statistics', [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notifications for donation status changes
     */
    private function sendDonationNotifications(Donation $donation, bool $wasSuccessful)
    {
        try {
            if ($wasSuccessful) {
                // Send success notification to donor
                $this->sendDonationSuccessNotification($donation);

                // Send notification to campaign owner
                $this->sendCampaignOwnerNotification($donation);

                // Send admin notification for large donations
                if ($donation->amount >= 1000000) { // Rp 1,000,000
                    $this->sendAdminLargeDonationNotification($donation);
                }
            } else {
                // Send failure notification to donor
                $this->sendDonationFailureNotification($donation);
            }

            Log::info('Donation notifications sent', [
                'donation_id' => $donation->id,
                'was_successful' => $wasSuccessful,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send donation notifications', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send success notification to donor
     */
    private function sendDonationSuccessNotification(Donation $donation)
    {
        try {
            $donation->user->notify(new DonationSuccessNotification($donation));

            Log::info('Donation success notification sent', [
                'donation_id' => $donation->id,
                'donor_email' => $donation->user->email,
                'donor_name' => $donation->user->name,
                'amount' => $donation->amount,
                'campaign_title' => $donation->campaign->title,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send success notification', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send notification to campaign owner
     */
    private function sendCampaignOwnerNotification(Donation $donation)
    {
        try {
            $campaignOwner = $donation->campaign->user;
            $campaignOwner->notify(new NewDonationReceivedNotification($donation));

            Log::info('Campaign owner notification sent', [
                'donation_id' => $donation->id,
                'campaign_id' => $donation->campaign->id,
                'owner_email' => $campaignOwner->email,
                'owner_name' => $campaignOwner->name,
                'donation_amount' => $donation->amount,
                'donor_name' => $donation->is_anonymous ? 'Anonymous' : $donation->user->name,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send campaign owner notification', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send admin notification for large donations
     */
    private function sendAdminLargeDonationNotification(Donation $donation)
    {
        try {
            // Get admin user (you can modify this to get admin users from database)
            $adminEmail = config('mail.admin_email');

            if ($adminEmail) {
                // Create a temporary admin user for notification
                $adminUser = new \App\Models\User;
                $adminUser->email = $adminEmail;
                $adminUser->name = 'Admin';

                $adminUser->notify(new LargeDonationAlertNotification($donation));
            }

            Log::info('Admin large donation notification sent', [
                'donation_id' => $donation->id,
                'amount' => $donation->amount,
                'campaign_title' => $donation->campaign->title,
                'donor_name' => $donation->is_anonymous ? 'Anonymous' : $donation->user->name,
                'admin_email' => $adminEmail,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send admin large donation notification', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send failure notification to donor
     */
    private function sendDonationFailureNotification(Donation $donation)
    {
        try {
            $donation->user->notify(new DonationFailureNotification($donation));

            Log::info('Donation failure notification sent', [
                'donation_id' => $donation->id,
                'donor_email' => $donation->user->email,
                'donor_name' => $donation->user->name,
                'amount' => $donation->amount,
                'campaign_title' => $donation->campaign->title,
                'status' => $donation->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send failure notification', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
