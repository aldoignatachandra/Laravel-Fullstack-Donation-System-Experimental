# 06 - Payment Integration (Midtrans)

## Overview

DonasiKita integrates with Midtrans payment gateway for secure online donations. Midtrans Snap provides a seamless payment experience with multiple payment methods.

## Configuration

### Environment Variables (.env)

```env
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_IS_PRODUCTION=false
```

### Config File (config/payment.php)

```php
return [
    'snap_redirect_base' => env('PAYMENT_SNAP_REDIRECT_BASE'),
    
    'midtrans' => [
        'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
        'server_key' => env('MIDTRANS_SERVER_KEY', ''),
        'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized' => true,
        'is_3ds' => true,
    ],
];
```

### Service Provider Setup

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    // Setup Midtrans configuration
    \Midtrans\Config::$serverKey = config('payment.midtrans.server_key');
    \Midtrans\Config::$isProduction = config('payment.midtrans.is_production');
    \Midtrans\Config::$isSanitized = config('payment.midtrans.is_sanitized');
    \Midtrans\Config::$is3ds = config('payment.midtrans.is_3ds');
}
```

## Payment Flow

### 1. Create Payment

```php
// In DonationService::recordDonation()
$params = [
    'transaction_details' => [
        'order_id' => $donation->order_id,      // Unique order ID
        'gross_amount' => (int) $donation->amount,  // Amount in IDR
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
        'duration' => 24,  // Payment expires in 24 hours
    ],
];

$snapToken = \Midtrans\Snap::getSnapToken($params);
$snapUrl = 'https://app.sandbox.midtrans.com/snap/v2/vtweb/' . $snapToken;
```

### 2. User Payment Experience

```
1. User clicks "Lanjut Pembayaran"
   ↓
2. Redirected to Midtrans Snap page
   ↓
3. User selects payment method:
   - Credit Card (Visa, Mastercard, JCB)
   - Virtual Account (BCA, BNI, BRI, Mandiri)
   - E-Wallet (GoPay, OVO, ShopeePay)
   - Convenience Store (Indomaret, Alfamart)
   - QRIS
   ↓
4. User completes payment
   ↓
5. Redirected back to /donation/payment
```

### 3. Payment Notification (Webhook)

```
Midtrans Server
   ↓
POST /midtrans/callback
   ↓
MidtransController::callback()
   ↓
DonationService::handleCallback()
   ↓
Update donation status
   ↓
Send notifications
   ↓
Return 200 OK
```

## Supported Payment Methods

| Method | Type | Status |
|--------|------|--------|
| Credit Card | Card | Active |
| BCA Virtual Account | Bank Transfer | Active |
| BNI Virtual Account | Bank Transfer | Active |
| BRI Virtual Account | Bank Transfer | Active |
| Mandiri Bill Payment | Bank Transfer | Active |
| GoPay | E-Wallet | Active |
| OVO | E-Wallet | Configurable |
| ShopeePay | E-Wallet | Configurable |
| QRIS | QR Code | Active |
| Indomaret | Convenience Store | Active |
| Alfamart | Convenience Store | Active |

## Transaction Statuses

### Midtrans Statuses

| Status | Description | Action |
|--------|-------------|--------|
| `pending` | Waiting for payment | Set donation to PENDING |
| `settlement` | Payment successful (credit card: captured) | Set donation to PAID |
| `capture` | Credit card captured | Set donation to PAID |
| `deny` | Payment denied by bank | Set donation to FAILED |
| `cancel` | Payment cancelled | Set donation to CANCELLED |
| `expire` | Payment expired | Set donation to FAILED |
| `failure` | Payment failed | Set donation to FAILED |
| `refund` | Payment refunded | Not implemented |
| `chargeback` | Chargeback occurred | Not implemented |

### Internal Status Mapping

```php
switch ($midtransStatus) {
    case 'settlement':
    case 'capture':
        $status = Donation::STATUS_PAID;
        break;
    case 'cancel':
        $status = Donation::STATUS_CANCELLED;
        break;
    case 'deny':
    case 'expire':
    case 'failure':
        $status = Donation::STATUS_FAILED;
        break;
    default:
        $status = Donation::STATUS_PENDING;
}
```

## Security Considerations

### 1. Order ID Uniqueness
```php
$orderId = 'ORD-' . time() . '-' . strtoupper(Str::random(6));
// Example: ORD-1704067200-ABC123
```

### 2. Amount Validation
```php
// Server-side validation
if ($amount < 10000 || $amount > 100000000) {
    throw new \Exception('Invalid amount');
}
```

### 3. Webhook Signature Verification (Production)
```php
// Verify callback is from Midtrans
$signatureKey = hash('sha512', 
    $orderId . $statusCode . $grossAmount . $serverKey
);

if ($signatureKey !== $request->signature_key) {
    abort(403, 'Invalid signature');
}
```

### 4. Row Locking
```php
// Prevent double-processing
$donation = Donation::lockForUpdate()
    ->where('order_id', $payload['order_id'])
    ->firstOrFail();
```

## Error Handling

### Common Errors

| Error | Cause | Solution |
|-------|-------|----------|
| Invalid order ID | Order not found | Check order_id format |
| Duplicate order | Order ID exists | Use unique order_id |
| Invalid amount | Amount < minimum | Validate before sending |
| Expired token | Token too old | Generate new token |
| Payment failed | Bank rejection | Show failure message |

### Retry Logic

Midtrans retries webhooks if not 200 OK:
- Retry 1: After 1 minute
- Retry 2: After 5 minutes  
- Retry 3: After 15 minutes

Our implementation handles duplicates via `$wasAlreadyPaid` check.

## Testing

### Sandbox Environment
```
Base URL: https://app.sandbox.midtrans.com
```

### Test Credentials

**Credit Card (Success):**
```
Card Number: 4811 1111 1111 1114
Expiry: 12/25
CVV: 123
OTP: 112233
```

**Credit Card (Failure):**
```
Card Number: 4911 1111 1111 1113
Expiry: 12/25
CVV: 123
```

**Virtual Account:**
Use any number, simulate payment in Midtrans dashboard.

### Testing Scenarios

```php
class PaymentTest extends TestCase
{
    public function test_successful_payment_callback()
    {
        $donation = Donation::factory()->create([
            'status' => Donation::STATUS_PENDING,
            'order_id' => 'ORD-TEST-123',
        ]);
        
        $response = $this->postJson('/midtrans/callback', [
            'order_id' => 'ORD-TEST-123',
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
        ]);
        
        $response->assertOk();
        
        $this->assertDatabaseHas('donations', [
            'id' => $donation->id,
            'status' => Donation::STATUS_PAID,
        ]);
    }
    
    public function test_failed_payment_callback()
    {
        $donation = Donation::factory()->create([
            'status' => Donation::STATUS_PENDING,
            'order_id' => 'ORD-TEST-456',
        ]);
        
        $response = $this->postJson('/midtrans/callback', [
            'order_id' => 'ORD-TEST-456',
            'transaction_status' => 'deny',
            'payment_type' => 'credit_card',
        ]);
        
        $response->assertOk();
        
        $this->assertDatabaseHas('donations', [
            'id' => $donation->id,
            'status' => Donation::STATUS_FAILED,
        ]);
    }
}
```

## Monitoring

### Log All Callbacks
```php
// In MidtransController::callback()
Log::info('Midtrans callback received', [
    'payload' => $request->all(),
    'ip' => $request->ip(),
]);
```

### Key Metrics to Track
- Success rate by payment method
- Average transaction time
- Failed transaction reasons
- Webhook delivery success rate

### Alerts
- Multiple failed payments from same user
- Webhook endpoint down
- Unusually large donations
