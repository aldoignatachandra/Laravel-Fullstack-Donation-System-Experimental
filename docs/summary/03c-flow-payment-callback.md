# 03c - Data Flow: Payment Callback (Webhook)

## Overview
How the system processes payment notifications from Midtrans via webhook.

## Webhook Endpoint

```php
// routes/web.php
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])
    ->name('midtrans.callback');
```

## Request Format

**Method**: POST  
**Content-Type**: application/json  
**Body**:
```json
{
  "order_id": "ORD-1704067200-ABC123",
  "transaction_status": "settlement",
  "payment_type": "credit_card",
  "transaction_id": "abc-123-def",
  "gross_amount": "100000",
  "transaction_time": "2023-12-31 23:59:59"
}
```

## Step-by-Step Flow

### Step 1: Receive Webhook

```php
// MidtransController::callback()
public function callback(Request $request)
{
    // Log for debugging
    Log::info('Midtrans callback received', $request->all());
    
    try {
        $donationService = app(DonationService::class);
        $result = $donationService->handleCallback($request->all());
        
        return response()->json([
            'status' => 'success',
            'message' => 'Callback processed successfully',
            'data' => $result
        ]);
    } catch (\Exception $e) {
        Log::error('Midtrans callback failed: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 400);
    }
}
```

### Step 2: Validate Payload

```php
// DonationService::handleCallback()
public function handleCallback(array $payload): Donation
{
    // Required fields
    if (empty($payload['order_id'])) {
        throw new \Exception('Order ID is required');
    }
    
    if (empty($payload['transaction_status'])) {
        throw new \Exception('Transaction status is required');
    }
}
```

### Step 3: Start Transaction

```php
DB::beginTransaction();
```

### Step 4: Lock and Find Donation

```php
// Lock donation row for update
$donation = Donation::lockForUpdate()
    ->where('order_id', $payload['order_id'])
    ->firstOrFail();

// Track if this is the first time being paid
$wasAlreadyPaid = $donation->status === Donation::STATUS_PAID;
```

### Step 5: Map Midtrans Status

```php
// Map Midtrans status to internal status
$midtransStatus = $payload['transaction_status'];
$paidAt = null;

switch ($midtransStatus) {
    case 'settlement':
        $status = Donation::STATUS_PAID;
        $paidAt = now();
        break;
        
    case 'capture':
        // For credit card capture
        $status = Donation::STATUS_PAID;
        $paidAt = now();
        break;
        
    case 'cancel':
        $status = Donation::STATUS_CANCELLED;
        break;
        
    case 'deny':
    case 'expire':
    case 'failure':
        $status = Donation::STATUS_FAILED;
        break;
        
    case 'pending':
    default:
        $status = Donation::STATUS_PENDING;
        break;
}
```

### Step 6: Update Donation Record

```php
$donation->update([
    'status' => $status,
    'payment_type' => $payload['payment_type'] ?? null,
    'paid_at' => $paidAt,
]);
```

### Step 7: Update Campaign Statistics

```php
// Only update stats if payment is successful and first time
if ($status === Donation::STATUS_PAID && !$wasAlreadyPaid) {
    $this->updateCampaignStatistics($donation->campaign);
}
```

### Step 8: Update Campaign Statistics (Detailed)

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
    
    // Check if target reached
    if ($totalAmount >= $campaign->target_amount 
        && $campaign->status === Campaign::STATUS_ACTIVE) {
        
        $campaign->update(['status' => Campaign::STATUS_COMPLETED]);
        
        Log::info("Campaign #{$campaign->id} completed! Target reached.");
    }
}
```

### Step 9: Send Notifications

```php
// Send notifications based on status
$this->sendDonationNotifications($donation, $status);
```

#### Notification Types

**If STATUS_PAID:**
```php
// 1. Success notification to donor
$donation->user->notify(new DonationSuccessNotification($donation));

// 2. Notification to campaign owner
$donation->campaign->user->notify(
    new NewDonationReceivedNotification($donation)
);

// 3. Alert for large donations (>= 1M)
if ($donation->amount >= 1000000) {
    $adminEmail = config('mail.admin_email');
    Notification::route('mail', $adminEmail)
        ->notify(new LargeDonationAlertNotification($donation));
}
```

**If STATUS_FAILED or STATUS_CANCELLED:**
```php
// Failure notification to donor
$donation->user->notify(new DonationFailureNotification($donation));
```

### Step 10: Commit Transaction

```php
DB::commit();

return $donation;
```

## Complete Flow Diagram

```
Midtrans
   |
   | POST /midtrans/callback
   | {order_id, transaction_status, payment_type, ...}
   v
MidtransController::callback()
   |
   |-- Log request
   v
DonationService::handleCallback()
   |
   |-- Validate payload
   |-- Begin transaction
   |-- Lock donation row
   |-- Find donation by order_id
   v
Status Mapping
   |
   |-- settlement/capture -> STATUS_PAID
   |-- cancel -> STATUS_CANCELLED
   |-- deny/expire/failure -> STATUS_FAILED
   |-- pending -> STATUS_PENDING
   v
Update Donation
   |
   |-- Set status
   |-- Set payment_type
   |-- Set paid_at (if paid)
   v
Update Campaign (if paid and first time)
   |
   |-- Calculate total donations
   |-- Calculate donor count
   |-- Check if target reached
   |-- Update status to COMPLETED if done
   v
Send Notifications
   |
   |-- Donor (success/failure)
   |-- Campaign owner (new donation)
   |-- Admin (large donation alert)
   v
Commit Transaction
   |
   v
Return JSON Response
   |
   {"status": "success", ...}
```

## Status Mapping Table

| Midtrans Status | Internal Status | Action |
|-----------------|-----------------|--------|
| settlement | PAID | Update stats, send notifications |
| capture | PAID | Update stats, send notifications |
| pending | PENDING | No action needed |
| cancel | CANCELLED | Send failure notification |
| deny | FAILED | Send failure notification |
| expire | FAILED | Send failure notification |
| failure | FAILED | Send failure notification |

## Webhook Security

### Current Implementation
- No signature verification (for sandbox)
- Relies on order_id lookup
- Transaction lock prevents double-processing

### Production Recommendations
```php
// Verify signature
$signatureKey = hash('sha512', 
    $request->order_id . 
    $request->status_code . 
    $request->gross_amount . 
    config('payment.midtrans.server_key')
);

if ($signatureKey !== $request->signature_key) {
    abort(403, 'Invalid signature');
}
```

## Error Handling

| Scenario | Response |
|----------|----------|
| Missing order_id | 400 Bad Request |
| Missing transaction_status | 400 Bad Request |
| Order not found | 404 Not Found |
| Database error | 500 Internal Error + Rollback |
| Already processed | 200 OK (idempotent) |

## Retry Logic

Midtrans retries webhooks if they don't receive 200 OK:
- First retry: 1 minute
- Second retry: 5 minutes
- Third retry: 15 minutes

Our implementation is idempotent - processing the same webhook multiple times won't cause issues due to:
1. `$wasAlreadyPaid` check
2. Database row locking
