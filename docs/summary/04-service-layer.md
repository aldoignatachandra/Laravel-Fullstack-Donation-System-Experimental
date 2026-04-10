# 04 - Service Layer Architecture

## Overview

The Service Layer pattern separates business logic from controllers, making code more testable, reusable, and maintainable.

## Location

```
app/Services/
└── DonationService.php    # All donation business logic
```

## DonationService

### Constants

```php
class DonationService
{
    // Minimum donation: Rp 10,000
    public const MIN_DONATION_AMOUNT = 10000;

    // Maximum donation: Rp 100,000,000
    public const MAX_DONATION_AMOUNT = 100000000;

    // Max 100 donations per user per day
    public const MAX_DAILY_DONATIONS_PER_USER = 100;

    // Max Rp 5,000,000 per user per day
    public const MAX_DAILY_AMOUNT_PER_USER = 5000000;
}
```

### Main Methods

#### 1. recordDonation()

**Purpose**: Create a new donation and generate payment link

**Parameters**:

- `$campaign` - Campaign model instance
- `$data` - Array with amount, message, is_anonymous

**Returns**: Array with success flag and snap_url

**Flow**:

```php
public function recordDonation(Campaign $campaign, array $data): array
{
    // 1. Validate business rules
    $this->validateBusinessRules($campaign, $data);

    // 2. Start database transaction
    DB::beginTransaction();

    try {
        // 3. Lock campaign row
        $campaign = Campaign::lockForUpdate()->find($campaign->id);

        // 4. Generate unique order ID
        $orderId = 'ORD-' . time() . '-' . strtoupper(Str::random(6));

        // 5. Create donation record
        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'user_id' => auth()->id(),
            'amount' => $data['amount'],
            'payment_method' => 'midtrans',
            'status' => Donation::STATUS_PENDING,
            'is_anonymous' => $data['is_anonymous'] ?? false,
            'message' => $data['message'] ?? null,
            'order_id' => $orderId,
        ]);

        // 6. Create payment link
        $snapUrl = $this->createSnapLink($donation);

        // 7. Commit transaction
        DB::commit();

        return [
            'success' => true,
            'snap_url' => $snapUrl,
        ];

    } catch (\Exception $e) {
        // Rollback on any error
        DB::rollBack();
        throw $e;
    }
}
```

---

#### 2. handleCallback()

**Purpose**: Process payment webhook from Midtrans

**Parameters**:

- `$payload` - Array from Midtrans webhook

**Returns**: Updated Donation model

**Flow**:

```php
public function handleCallback(array $payload): Donation
{
    // 1. Validate required fields
    if (empty($payload['order_id'])) {
        throw new \Exception('Order ID is required');
    }

    // 2. Start transaction
    DB::beginTransaction();

    try {
        // 3. Lock and find donation
        $donation = Donation::lockForUpdate()
            ->where('order_id', $payload['order_id'])
            ->firstOrFail();

        // 4. Check if already processed
        $wasAlreadyPaid = $donation->status === Donation::STATUS_PAID;

        // 5. Map status
        $status = $this->mapMidtransStatus($payload['transaction_status']);
        $paidAt = $status === Donation::STATUS_PAID ? now() : null;

        // 6. Update donation
        $donation->update([
            'status' => $status,
            'payment_type' => $payload['payment_type'] ?? null,
            'paid_at' => $paidAt,
        ]);

        // 7. Update campaign stats if newly paid
        if ($status === Donation::STATUS_PAID && !$wasAlreadyPaid) {
            $this->updateCampaignStatistics($donation->campaign);
        }

        // 8. Send notifications
        $this->sendDonationNotifications($donation, $status);

        // 9. Commit
        DB::commit();

        return $donation;

    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}
```

---

#### 3. createSnapLink()

**Purpose**: Generate Midtrans Snap payment URL

**Parameters**:

- `$donation` - Donation model instance

**Returns**: Payment URL string

```php
private function createSnapLink(Donation $donation): string
{
    $params = [
        'transaction_details' => [
            'order_id' => $donation->order_id,
            'gross_amount' => (int) $donation->amount,
        ],
        'customer_details' => [
            'first_name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ],
        'item_details' => [
            [
                'id' => $donation->campaign->slug,
                'price' => (int) $donation->amount,
                'quantity' => 1,
                'name' => substr($donation->campaign->title, 0, 50),
            ]
        ],
        'callbacks' => [
            'finish' => route('donation.payment'),
        ],
        'expiry' => [
            'unit' => 'hour',
            'duration' => 24,
        ],
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken;
}
```

---

#### 4. validateBusinessRules()

**Purpose**: Validate donation against business constraints

**Checks**:

1. Campaign is active
2. Campaign has started
3. Campaign hasn't ended
4. Amount is within limits
5. User hasn't exceeded daily limits
6. Not a duplicate (within 5 minutes)

```php
private function validateBusinessRules(Campaign $campaign, array $data): void
{
    // Campaign status
    if ($campaign->status !== Campaign::STATUS_ACTIVE) {
        throw new \Exception('Campaign is not active');
    }

    // Date range
    $today = now()->startOfDay();
    if ($today->lt($campaign->start_date)) {
        throw new \Exception('Campaign has not started');
    }
    if ($today->gt($campaign->end_date)) {
        throw new \Exception('Campaign has ended');
    }

    // Amount bounds
    $amount = $data['amount'];
    if ($amount < self::MIN_DONATION_AMOUNT) {
        throw new \Exception('Minimum donation is Rp 10,000');
    }
    if ($amount > self::MAX_DONATION_AMOUNT) {
        throw new \Exception('Maximum donation is Rp 100,000,000');
    }

    // Daily donation count limit
    $todayDonations = Donation::where('user_id', auth()->id())
        ->whereDate('created_at', today())
        ->count();
    if ($todayDonations >= self::MAX_DAILY_DONATIONS_PER_USER) {
        throw new \Exception('Daily donation limit reached (100)');
    }

    // Daily amount limit
    $todayAmount = Donation::where('user_id', auth()->id())
        ->whereDate('created_at', today())
        ->sum('amount');
    if (($todayAmount + $amount) > self::MAX_DAILY_AMOUNT_PER_USER) {
        throw new \Exception('Daily amount limit reached (Rp 5,000,000)');
    }

    // Duplicate check (5 minute window)
    $recent = Donation::where('user_id', auth()->id())
        ->where('campaign_id', $campaign->id)
        ->where('amount', $amount)
        ->where('created_at', '>=', now()->subMinutes(5))
        ->first();
    if ($recent) {
        throw new \Exception('Duplicate donation detected');
    }
}
```

---

#### 5. updateCampaignStatistics()

**Purpose**: Update campaign totals and check completion

```php
private function updateCampaignStatistics(Campaign $campaign): void
{
    // Calculate totals
    $totalAmount = Donation::where('campaign_id', $campaign->id)
        ->where('status', Donation::STATUS_PAID)
        ->sum('amount');

    $totalDonors = Donation::where('campaign_id', $campaign->id)
        ->where('status', Donation::STATUS_PAID)
        ->count();

    // Auto-complete if target reached
    if ($totalAmount >= $campaign->target_amount
        && $campaign->status === Campaign::STATUS_ACTIVE) {

        $campaign->update(['status' => Campaign::STATUS_COMPLETED]);

        Log::info("Campaign #{$campaign->id} completed!");
    }
}
```

---

#### 6. Notification Methods

```php
private function sendDonationNotifications(Donation $donation, int $status): void
{
    if ($status === Donation::STATUS_PAID) {
        $this->sendDonationSuccessNotification($donation);
        $this->sendCampaignOwnerNotification($donation);

        if ($donation->amount >= 1000000) {
            $this->sendAdminLargeDonationNotification($donation);
        }
    } elseif (in_array($status, [Donation::STATUS_FAILED, Donation::STATUS_CANCELLED])) {
        $this->sendDonationFailureNotification($donation);
    }
}
```

## Design Patterns Used

### 1. Single Responsibility

Each method does one thing well:

- `recordDonation()` - Creates donation
- `handleCallback()` - Processes webhook
- `validateBusinessRules()` - Validates constraints

### 2. Transaction Script

Complex operations wrapped in transactions:

```php
DB::beginTransaction();
try {
    // multiple operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### 3. Pessimistic Locking

Prevents race conditions:

```php
Campaign::lockForUpdate()->find($id);
```

### 4. Fail Fast

Validates early, throws immediately:

```php
if ($invalid) {
    throw new \Exception('Error');
}
```

## Dependency Injection

Services are injected where needed:

```php
class DonationForm extends Component
{
    protected DonationService $donationService;

    public function boot(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    public function proceedToPayment()
    {
        $result = $this->donationService->recordDonation($campaign, $data);
    }
}
```

## Benefits of Service Layer

1. **Testability**: Can unit test business logic without HTTP layer
2. **Reusability**: Same service used in web, API, and CLI contexts
3. **Maintainability**: Business logic in one place
4. **Readability**: Controllers stay thin and readable
5. **Transaction Safety**: Centralized transaction management
