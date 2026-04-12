# 🚀 DonasiKita Code Improvement Plan

> **Document Version:** 1.0  
> **Created:** 2026-04-11  
> **Last Updated:** 2026-04-11  
> **Status:** Active Development

This document tracks all planned code improvements for the DonasiKita project, organized by priority phases. Each item includes implementation notes and a checkbox for tracking progress.

---

## 📋 How to Use This Plan

1. **Phase Order:** Complete items in phase order (Phase 1 → Phase 2 → Phase 3 → Phase 4)
2. **Status Tracking:** Check off items as they are implemented
3. **Progress Updates:** Update the "Progress Summary" section after each session
4. **Notes:** Add implementation notes in the "Implementation Log" at the bottom

---

## 📊 Progress Summary

```
Phase 1 (Critical):    [0/3]   0%  ⬜⬜⬜
Phase 2 (High):        [0/4]   0%  ⬜⬜⬜⬜
Phase 3 (Medium):      [0/6]   0%  ⬜⬜⬜⬜⬜⬜
Phase 4 (Low):         [0/5]   0%  ⬜⬜⬜⬜⬜

Overall Progress:      [0/18]  0%
```

---

## Phase 1: Critical Issues 🔴

**Priority:** MUST FIX before next release  
**Estimated Time:** 30 minutes  
**Impact:** Prevents bugs, improves type safety

### 1.1 Add Missing Return Type Declarations

**Files:**
- [ ] `app/Services/DonationService.php`
- [ ] `app/Livewire/Dashboard/Donations.php`

**Tasks:**
- [ ] Add `: array` return type to `DonationService::recordDonation()`
- [ ] Add `: ?Donation` return type to `DonationService::handleCallback()`
- [ ] Add return type to `Donations::getDonations()` - should be `LengthAwarePaginator`
- [ ] Add return type to `Donations::formatDate()` - should be `string`

**Implementation Notes:**
```php
// Before:
public function recordDonation(Campaign $campaign, array $data)
public function handleCallback($payload)

// After:
public function recordDonation(Campaign $campaign, array $data): array
public function handleCallback($payload): ?Donation
```

---

### 1.2 Add Model Fillable Arrays

**Files:**
- [ ] `app/Models/Campaign.php`
- [ ] `app/Models/Donation.php`
- [ ] `app/Models/CampaignCategory.php`
- [ ] `app/Models/CampaignArticle.php`

**Tasks:**
- [ ] Add `$fillable` array to Campaign model
- [ ] Add `$fillable` array to Donation model
- [ ] Add `$casts()` method with proper type casting
- [ ] Verify all models use `casts()` method instead of `$casts` property (Laravel 11+ best practice)

**Implementation Notes:**
```php
// Campaign.php
protected $fillable = [
    'campaign_category_id',
    'user_id',
    'title',
    'slug',
    'description',
    'target_amount',
    'start_date',
    'end_date',
    'status',
    'is_featured',
];

protected function casts(): array
{
    return [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_amount' => 'decimal:2',
        'is_featured' => 'boolean',
    ];
}
```

---

### 1.3 Fix Inconsistent Foreign Key Naming

**Files:**
- [ ] `tests/Unit/Models/CampaignTest.php` (already fixed)
- [ ] `tests/Feature/CampaignPageTest.php` (already fixed)
- [ ] `database/factories/CampaignFactory.php`

**Tasks:**
- [ ] Verify all references use `campaign_category_id` consistently
- [ ] Remove any remaining references to `category_id` in factories/tests
- [ ] Update any documentation/comments

**Status:** ✅ Already fixed in tests, verify factories

---

## Phase 2: High Priority Improvements 🟠

**Priority:** Should fix for better maintainability  
**Estimated Time:** 2-3 hours  
**Impact:** Significantly improves code organization

### 2.1 Split DonationService (Refactor)

**Current Problem:**
- File: `app/Services/DonationService.php` (544 lines, 12 methods)
- Violates Single Responsibility Principle
- Too many responsibilities: donations, payments, webhooks, notifications

**Refactor Plan:**

```
app/Services/
├── Donation/
│   ├── DonationCreator.php         # recordDonation, validation
│   ├── DonationValidator.php       # validateBusinessRules
│   └── DonationNotifier.php        # All notification methods
├── Payment/
│   └── MidtransService.php         # createSnapLink, Midtrans config
└── Webhook/
    └── PaymentWebhookHandler.php   # handleCallback
```

**Tasks:**
- [ ] Create `app/Services/Donation/DonationCreator.php`
  - Move: `recordDonation()`
  - Move: `validateBusinessRules()`
  - Move: `generateOrderId()`
  
- [ ] Create `app/Services/Donation/DonationNotifier.php`
  - Move: `sendDonationNotifications()`
  - Move: `sendDonationSuccessNotification()`
  - Move: `sendCampaignOwnerNotification()`
  - Move: `sendAdminLargeDonationNotification()`
  - Move: `sendDonationFailureNotification()`
  
- [ ] Create `app/Services/Payment/MidtransService.php`
  - Move: `createSnapLink()`
  - Move: Midtrans config from constructor
  
- [ ] Create `app/Services/Webhook/PaymentWebhookHandler.php`
  - Move: `handleCallback()`
  - Move: `updateCampaignStatistics()`
  
- [ ] Update `DonationService.php` to use new services
- [ ] Update all references in controllers/Livewire
- [ ] Run tests to ensure nothing breaks

**Implementation Notes:**
```php
// New DonationCreator service
class DonationCreator
{
    public function __construct(
        private DonationValidator $validator,
        private MidtransService $midtrans,
        private DonationNotifier $notifier
    ) {}
    
    public function create(Campaign $campaign, array $data): array
    {
        // Implementation
    }
}
```

---

### 2.2 Extract Configuration Constants

**Files:**
- [ ] `config/donation.php` (create new)
- [ ] `app/Livewire/LandingPage.php`
- [ ] `app/Livewire/Dashboard/Donations.php`
- [ ] `app/Services/DonationService.php`

**Tasks:**
- [ ] Create `config/donation.php` with pagination settings
- [ ] Replace hardcoded `->paginate(12)` with config value
- [ ] Replace hardcoded `->paginate(5)` with config value
- [ ] Consider moving service constants to config

**Implementation Notes:**
```php
// config/donation.php
return [
    'pagination' => [
        'campaigns_per_page' => 12,
        'donations_per_page' => 5,
    ],
    'limits' => [
        'min_donation' => 10000,
        'max_donation' => 100000000,
        'max_daily_donations' => 100,
        'max_daily_amount' => 5000000,
    ],
];
```

---

### 2.3 Create Form Request Classes

**Files:**
- [ ] `app/Http/Requests/StoreDonationRequest.php`
- [ ] `app/Livewire/Campaign/DonationForm.php`

**Tasks:**
- [ ] Create Form Request for donation validation
- [ ] Move validation rules from Livewire to Form Request
- [ ] Update Livewire component to use Form Request
- [ ] Add authorization logic to Form Request

**Implementation Notes:**
```php
class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:10000', 'max:100000000'],
            'message' => ['nullable', 'string', 'max:500'],
            'is_anonymous' => ['boolean'],
        ];
    }
}
```

---

### 2.4 Add API Rate Limiting

**Files:**
- [ ] `routes/api.php`
- [ ] `app/Http/Kernel.php` (if custom rate limiter needed)

**Tasks:**
- [ ] Add rate limiting to webhook endpoint
- [ ] Consider rate limiting for donation creation
- [ ] Document rate limits in README

**Implementation Notes:**
```php
// routes/api.php
Route::post('/webhook/midtrans', [MidtransController::class, 'callback'])
    ->middleware('throttle:webhook')  // Custom named rate limiter
    ->name('webhook.midtrans');

// In RouteServiceProvider or bootstrap/app.php
RateLimiter::for('webhook', function (Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});
```

---

## Phase 3: Medium Priority Enhancements 🟡

**Priority:** Nice to have, improves code quality  
**Estimated Time:** 4-6 hours  
**Impact:** Better developer experience, modern PHP features

### 3.1 Create PHP 8.1+ Enum Classes

**Files:**
- [ ] `app/Enums/DonationStatus.php` (create new)
- [ ] `app/Enums/CampaignStatus.php` (create new)
- [ ] `app/Models/Donation.php`
- [ ] `app/Models/Campaign.php`
- [ ] All files using status constants

**Tasks:**
- [ ] Create `app/Enums/DonationStatus.php`
- [ ] Create `app/Enums/CampaignStatus.php`
- [ ] Replace all `STATUS_*` constants with Enum cases
- [ ] Add label methods to Enums for display text
- [ ] Update database queries to use Enums
- [ ] Run full test suite after changes

**Implementation Notes:**
```php
// app/Enums/DonationStatus.php
namespace App\Enums;

enum DonationStatus: int
{
    case PENDING = 0;
    case PAID = 1;
    case FAILED = 2;
    case CANCELLED = 3;
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Berhasil',
            self::FAILED => 'Gagal',
            self::CANCELLED => 'Dibatalkan',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }
}

// Usage in model:
public function status(): DonationStatus
{
    return DonationStatus::from($this->status);
}
```

---

### 3.2 Add Comprehensive PHPDoc Blocks

**Files:**
- [ ] `app/Services/DonationService.php` - All methods
- [ ] `app/Http/Controllers/MidtransController.php` - Callback flow methods
- [ ] `app/Livewire/Campaign/DonationForm.php` - All methods
- [ ] `app/Livewire/Campaign/ShowCampaign.php` - All methods
- [ ] All Helper classes

**Tasks:**
- [ ] Add PHPDoc to complex methods (validateBusinessRules, handleCallback)
- [ ] Document parameters with types
- [ ] Document return values
- [ ] Document exceptions thrown
- [ ] Add example usage where helpful

**Example:**
```php
/**
 * Validate business rules before creating a donation.
 *
 * Checks:
 * - Campaign is active and not expired
 * - Donation amount within min/max limits
 * - User hasn't exceeded daily limits
 * - Campaign hasn't reached its target
 *
 * @param Campaign $campaign The campaign receiving the donation
 * @param array $data Donation data including 'amount', 'message', etc.
 * @return void
 * @throws ValidationException If any business rule is violated
 * @throws RuntimeException If campaign is no longer available
 */
private function validateBusinessRules(Campaign $campaign, array $data): void
```

---

### 3.3 Implement Query Optimization

**Files:**
- [ ] `app/Livewire/Dashboard/Donations.php`
- [ ] `app/Livewire/LandingPage.php`
- [ ] `app/Livewire/Campaign/ShowCampaign.php`

**Tasks:**
- [ ] Add database indexes for frequently searched columns
- [ ] Optimize `whereHas` queries (consider denormalization for search)
- [ ] Add query caching for expensive operations
- [ ] Use `select()` to limit columns retrieved
- [ ] Consider Laravel Scout for full-text search

**Implementation Notes:**
```php
// Add index in migration
$table->index('title', 'campaigns_title_index');
$table->fullText(['title', 'description']); // For MySQL 5.6+

// Optimize query
$campaigns = Campaign::query()
    ->select(['id', 'title', 'slug', 'image', 'target_amount', 'status'])
    ->withCount(['donations as total_donations' => fn($q) => $q->where('status', 1)])
    ->active()
    ->paginate(12);
```

---

### 3.4 Add Missing Indexes to Database

**Files:**
- [ ] Create new migration: `add_performance_indexes.php`

**Indexes to Add:**
- [ ] `campaigns.status` - Frequently filtered
- [ ] `campaigns.campaign_category_id` - Joins
- [ ] `campaigns.slug` - Lookups (should already exist)
- [ ] `donations.user_id` - User's donation queries
- [ ] `donations.campaign_id` - Campaign donation queries
- [ ] `donations.status` - Status filtering
- [ ] `donations.order_id` - Webhook lookups
- [ ] `donations.created_at` - Ordering
- [ ] `campaigns.title` - Search (fulltext if MySQL)

**Implementation:**
```php
Schema::table('campaigns', function (Blueprint $table) {
    $table->index('status');
    $table->index('campaign_category_id');
    $table->index(['status', 'is_featured']); // Composite index
});

Schema::table('donations', function (Blueprint $table) {
    $table->index(['user_id', 'status']);
    $table->index('order_id');
    $table->index('created_at');
});
```

---

### 3.5 Create Service Provider for Bindings

**Files:**
- [ ] `app/Providers/DonationServiceProvider.php` (create new)
- [ ] `bootstrap/providers.php`

**Tasks:**
- [ ] Create service provider for dependency injection
- [ ] Bind interfaces to implementations (if using interfaces)
- [ ] Register singleton services
- [ ] Add to providers array

**Implementation Notes:**
```php
class DonationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MidtransService::class, function ($app) {
            return new MidtransService(
                config('payment.midtrans.server_key'),
                config('payment.midtrans.is_production')
            );
        });
    }
}
```

---

### 3.6 Add Event/Listener for Key Actions

**Files:**
- [ ] `app/Events/DonationCreated.php`
- [ ] `app/Events/DonationPaid.php`
- [ ] `app/Listeners/SendDonationNotifications.php`
- [ ] `app/Listeners/UpdateCampaignStats.php`

**Tasks:**
- [ ] Create events for donation lifecycle
- [ ] Move notification logic to listeners
- [ ] Move stats update to listeners
- [ ] Dispatch events from services

**Benefits:**
- Better separation of concerns
- Easier to add new features (just add listeners)
- Can queue listeners for better performance

---

## Phase 4: Low Priority Polish 🟢

**Priority:** Nice to have, cosmetic improvements  
**Estimated Time:** 2-3 hours  
**Impact:** Code cleanup, consistency

### 4.1 Clean Up Unused Imports

**Files:**
- [ ] All PHP files in `app/`

**Tasks:**
- [ ] Run `vendor/bin/pint` (Pint handles this automatically)
- [ ] Or run `vendor/bin/pint --dirty`
- [ ] Verify no unused imports remain

**Status:** ✅ Can be done automatically with Pint

---

### 4.2 Standardize Code Style

**Files:**
- [ ] All PHP files

**Tasks:**
- [ ] Run `vendor/bin/pint` to fix all style issues
- [ ] Configure Pint rules if needed in `pint.json`
- [ ] Add pre-commit hook to run Pint automatically

**Implementation:**
```bash
# Run on all files
vendor/bin/pint

# Run only on changed files
vendor/bin/pint --dirty

# Check without fixing
vendor/bin/pint --test
```

---

### 4.3 Add Type Declarations to All Properties

**Files:**
- [ ] `app/Livewire/` all components
- [ ] `app/Services/` all services
- [ ] `app/Models/` all models

**Tasks:**
- [ ] Add typed properties where missing
- [ ] Use `private`, `protected`, `public` consistently
- [ ] Add `readonly` where applicable (PHP 8.1+)

**Example:**
```php
class DonationForm extends Component
{
    public Campaign $campaign;
    
    #[Validate('required|numeric|min:10000')]
    public int $selectedAmount = 0;
    
    #[Validate('nullable|string|max:500')]
    public string $message = '';
    
    public bool $isAnonymous = false;
    
    private DonationService $donationService;
}
```

---

### 4.4 Refactor Helper Classes

**Files:**
- [ ] `app/Helper/CampaignHelper.php`
- [ ] `app/Helper/NumberHelper.php`

**Tasks:**
- [ ] Consider making methods non-static
- [ ] Add interface for better testability
- [ ] Move to `app/Support/` or `app/Utils/` namespace
- [ ] Add comprehensive tests

**Rationale:**
Static methods are harder to mock in tests. Instance-based helpers are more flexible.

---

### 4.5 Add Comprehensive Logging

**Files:**
- [ ] `app/Services/DonationService.php`
- [ ] `app/Http/Controllers/MidtransController.php`
- [ ] `app/Livewire/Campaign/DonationForm.php`

**Tasks:**
- [ ] Add context to all log messages
- [ ] Use appropriate log levels (debug, info, warning, error)
- [ ] Add request IDs for tracing
- [ ] Log all payment webhook events

**Implementation:**
```php
Log::info('Processing donation', [
    'request_id' => $requestId,
    'campaign_id' => $campaign->id,
    'user_id' => auth()->id(),
    'amount' => $data['amount'],
]);
```

---

## 📝 Implementation Log

Use this section to track what's been completed and any issues encountered.

### 2026-04-11 - Initial Plan Created
- [x] Created comprehensive improvement plan
- [x] Organized by priority phases
- [ ] Phase 1: Not started
- [ ] Phase 2: Not started
- [ ] Phase 3: Not started
- [ ] Phase 4: Not started

### Session Notes Template
```
Date: YYYY-MM-DD
Completed Items:
- [Item number]: [Brief description]

Issues Encountered:
- [Any blockers or issues]

Next Steps:
- [What to work on next]

Progress Update:
Phase 1: [X/3] completed
Phase 2: [X/4] completed
Overall: [X/18] completed
```

---

## 📚 References

### Laravel Best Practices
- [Laravel 12.x Documentation](https://laravel.com/docs/12.x)
- [Laravel Testing Best Practices](https://laravel.com/docs/12.x/testing)
- [PHP 8.4 Features](https://www.php.net/releases/8.4/en.php)

### Code Quality Tools
- **Laravel Pint:** Code style fixer (`vendor/bin/pint`)
- **PHPStan:** Static analysis (level 8 recommended)
- **Rector:** Automated refactoring

### Testing Resources
- Current coverage: 22.2%
- Target coverage: 70%
- Test location: `tests/`
- Run tests: `composer test`
- Run coverage: `composer test:coverage`

---

## ✅ Definition of Done

For each item to be considered complete:

1. **Code Written:** Implementation follows Laravel best practices
2. **Tests Pass:** All existing tests still pass (`composer test`)
3. **Pint Clean:** Code style passes (`vendor/bin/pint --test`)
4. **Documented:** PHPDoc added for public methods
5. **Tested:** New functionality has unit/feature tests
6. **Checked Off:** Item marked complete in this plan

---

**End of Document**

Last Updated: 2026-04-11
