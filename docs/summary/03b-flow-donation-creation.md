# 03b - Data Flow: Donation Creation

## Overview
Complete flow from user clicking "Donate" to payment redirect.

## Step-by-Step Flow

### Phase 1: Access Donation Form

**Step 1.1: User Clicks Donate Button**
- **From**: Campaign detail page
- **URL**: GET /campaign/{slug}/donate
- **Middleware**: auth (must be logged in)

**Step 1.2: Route Handler**
```php
Route::get('/campaign/{slug}/donate', DonationForm::class)
    ->middleware('auth')
    ->name('campaign.donate');
```

**Step 1.3: Component Mount**
```php
// DonationForm::mount($slug)
public function mount($slug)
{
    $this->slug = $slug;
    
    // Load campaign
    $this->campaign = Campaign::where('slug', $slug)
        ->where('status', Campaign::STATUS_ACTIVE)
        ->firstOrFail();
    
    // Initialize form state
    $this->selectedAmount = null;
    $this->customAmount = '';
    $this->message = '';
    $this->isAnonymous = false;
}
```

---

### Phase 2: User Fills Form

**Step 2.1: Amount Selection**
- User can either:
  a) Click predefined amount button (100k, 200k, 300k, 400k, 500k, 1M)
  b) Type custom amount in input field

**Step 2.2: Amount Validation**
```php
public function updatedCustomAmount($value)
{
    // Remove non-numeric characters
    $this->customAmount = preg_replace('/[^0-9]/', '', $value);
    
    // Set as selected amount
    if ($this->customAmount > 0) {
        $this->selectedAmount = (int) $this->customAmount;
    }
}
```

**Step 2.3: Optional Fields**
- Message (max 500 characters)
- Anonymous checkbox

---

### Phase 3: Form Submission

**Step 3.1: User Clicks Submit**
```php
public function proceedToPayment()
{
    // Validation
    $this->validate([
        'selectedAmount' => ['required', 'numeric', 'min:10000'],
    ]);
    
    // Call service
    $result = $this->donationService->recordDonation(
        $this->campaign,
        [
            'amount' => $this->selectedAmount,
            'message' => $this->message,
            'is_anonymous' => $this->isAnonymous,
        ]
    );
    
    // Redirect to payment
    if ($result['success']) {
        return redirect()->away($result['snap_url']);
    }
}
```

---

### Phase 4: Business Logic (DonationService)

**Step 4.1: Validate Business Rules**
```php
private function validateBusinessRules(Campaign $campaign, array $data): void
{
    // Campaign must be active
    if ($campaign->status !== Campaign::STATUS_ACTIVE) {
        throw new \Exception('Campaign is not active');
    }
    
    // Check dates
    $today = now()->startOfDay();
    if ($today->lt($campaign->start_date)) {
        throw new \Exception('Campaign has not started yet');
    }
    if ($today->gt($campaign->end_date)) {
        throw new \Exception('Campaign has ended');
    }
    
    // Amount validation
    $amount = $data['amount'];
    if ($amount < self::MIN_DONATION_AMOUNT) { // 10000
        throw new \Exception('Minimum donation is Rp 10,000');
    }
    if ($amount > self::MAX_DONATION_AMOUNT) { // 100000000
        throw new \Exception('Maximum donation is Rp 100,000,000');
    }
    
    // Daily limits per user
    $todayDonations = Donation::where('user_id', auth()->id())
        ->whereDate('created_at', today())
        ->count();
    if ($todayDonations >= self::MAX_DAILY_DONATIONS_PER_USER) { // 100
        throw new \Exception('Daily donation limit reached');
    }
    
    // Daily amount limit
    $todayAmount = Donation::where('user_id', auth()->id())
        ->whereDate('created_at', today())
        ->sum('amount');
    if (($todayAmount + $amount) > self::MAX_DAILY_AMOUNT_PER_USER) { // 5000000
        throw new \Exception('Daily amount limit reached');
    }
    
    // Duplicate check (5 minute window)
    $recentDonation = Donation::where('user_id', auth()->id())
        ->where('campaign_id', $campaign->id)
        ->where('amount', $amount)
        ->where('created_at', '>=', now()->subMinutes(5))
        ->first();
    if ($recentDonation) {
        throw new \Exception('Duplicate donation detected');
    }
}
```

**Step 4.2: Database Transaction**
```php
DB::beginTransaction();

try {
    // Lock campaign row
    $campaign = Campaign::lockForUpdate()->find($campaign->id);
    
    // Generate order ID
    $orderId = 'ORD-' . time() . '-' . strtoupper(Str::random(6));
    // Example: ORD-1704067200-ABC123
    
    // Create donation record
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
    
    // Create payment link
    $snapUrl = $this->createSnapLink($donation);
    
    DB::commit();
    
    return [
        'success' => true,
        'snap_url' => $snapUrl,
    ];
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

**Step 4.3: Create Snap Payment Link**
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

### Phase 5: Payment Redirect

**Step 5.1: User Redirected**
```php
return redirect()->away($result['snap_url']);
```

**Step 5.2: User Pays on Midtrans**
- Credit card
- Bank transfer (Virtual Account)
- E-wallet (GoPay, OVO, etc.)
- Convenience store

**Step 5.3: Return to Application**
- Midtrans redirects to: `/donation/payment`
- Shows success/failure message

---

## Complete Data Flow Summary

```
1. User clicks "Donate"
   -> /campaign/{slug}/donate
   
2. DonationForm component loads
   -> Validates campaign is ACTIVE
   -> Initializes form state
   
3. User fills form
   -> Selects amount
   -> Adds message (optional)
   -> Chooses anonymous (optional)
   
4. User submits
   -> Validation (amount >= 10000)
   -> DonationService::recordDonation()
   
5. Business validation
   -> Campaign status check
   -> Date range check
   -> Amount limits
   -> Daily user limits
   -> Duplicate prevention
   
6. Database transaction
   -> Lock campaign row
   -> Generate order ID
   -> Create PENDING donation
   -> Call Midtrans API
   -> Commit transaction
   
7. Redirect to payment
   -> User pays on Midtrans
   -> Returns to application
```

## Key Security Measures

1. **Row Locking**: `lockForUpdate()` prevents race conditions
2. **Transactions**: All-or-nothing database operations
3. **Business Rules**: Server-side validation of all constraints
4. **Order ID**: Unique identifier for tracking
5. **Daily Limits**: Prevents abuse
6. **Duplicate Detection**: 5-minute window check
