# 09 - Testing & Development

## Testing Setup

### Configuration

**File**: `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>
```

### Test Database

Uses SQLite in-memory database for fast tests:
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## Running Tests

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/CampaignPageTest.php

# Run specific test method
php artisan test --filter=test_user_can_create_donation

# Run with parallel processing
php artisan test --parallel
```

## Test Structure

```
tests/
├── Feature/                   # Integration tests
│   ├── Auth/                  # Authentication tests
│   ├── Settings/              # User settings tests
│   ├── CampaignPageTest.php
│   └── DashboardTest.php
├── Unit/                      # Unit tests
│   ├── Models/                # Model tests
│   ├── Services/              # Service tests
│   ├── Livewire/              # Livewire component tests
│   ├── Notifications/         # Notification tests
│   └── Helper/                # Helper tests
├── Pest.php                   # Pest bootstrap/config
└── TestCase.php               # Base test class
```

## Example Tests

### Authentication Test

```php
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
    
    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
    
    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();
        
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        
        $this->assertGuest();
    }
}
```

### Livewire Component Test

```php
class DonationFormTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_donation_form_renders_successfully()
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(DonationForm::class, ['slug' => $campaign->slug])
            ->assertStatus(200)
            ->assertSee($campaign->title);
    }
    
    public function test_user_can_select_donation_amount()
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(DonationForm::class, ['slug' => $campaign->slug])
            ->set('selectedAmount', 100000)
            ->assertSet('selectedAmount', 100000);
    }
    
    public function test_donation_fails_with_amount_below_minimum()
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        
        Livewire::actingAs($user)
            ->test(DonationForm::class, ['slug' => $campaign->slug])
            ->set('selectedAmount', 1000)  // Below minimum
            ->call('proceedToPayment')
            ->assertHasErrors(['selectedAmount' => 'min']);
    }
}
```

### Service Layer Test

```php
class DonationServiceTest extends TestCase
{
    use RefreshDatabase;
    
    protected DonationService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DonationService();
    }
    
    public function test_record_donation_creates_pending_donation()
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $result = $this->service->recordDonation($campaign, [
            'amount' => 50000,
            'message' => 'Good luck!',
            'is_anonymous' => false,
        ]);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('snap_url', $result);
        
        $this->assertDatabaseHas('donations', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'amount' => 50000,
            'status' => Donation::STATUS_PENDING,
        ]);
    }
    
    public function test_handle_callback_updates_donation_status()
    {
        $donation = Donation::factory()->create([
            'status' => Donation::STATUS_PENDING,
            'order_id' => 'ORD-TEST-123',
        ]);
        
        $result = $this->service->handleCallback([
            'order_id' => 'ORD-TEST-123',
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card',
        ]);
        
        $this->assertEquals(Donation::STATUS_PAID, $result->status);
        $this->assertNotNull($result->paid_at);
    }
    
    public function test_cannot_donate_to_inactive_campaign()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Campaign is not active');
        
        $campaign = Campaign::factory()->create([
            'status' => Campaign::STATUS_DRAFT,
        ]);
        $user = User::factory()->create();
        
        $this->actingAs($user);
        
        $this->service->recordDonation($campaign, [
            'amount' => 50000,
        ]);
    }
}
```

### Model Test

```php
class CampaignTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_campaign_has_category()
    {
        $category = CampaignCategory::factory()->create();
        $campaign = Campaign::factory()->create([
            'campaign_category_id' => $category->id,
        ]);
        
        $this->assertInstanceOf(CampaignCategory::class, $campaign->category);
        $this->assertEquals($category->id, $campaign->category->id);
    }
    
    public function test_campaign_has_donations()
    {
        $campaign = Campaign::factory()->create();
        $donations = Donation::factory()->count(3)->create([
            'campaign_id' => $campaign->id,
        ]);
        
        $this->assertCount(3, $campaign->donations);
    }
    
    public function test_campaign_calculates_total_donations()
    {
        $campaign = Campaign::factory()->create();
        
        Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 100000,
            'status' => Donation::STATUS_PAID,
        ]);
        
        Donation::factory()->create([
            'campaign_id' => $campaign->id,
            'amount' => 50000,
            'status' => Donation::STATUS_PAID,
        ]);
        
        $total = $campaign->donations()
            ->where('status', Donation::STATUS_PAID)
            ->sum('amount');
        
        $this->assertEquals(150000, $total);
    }
}
```

## Factories

### User Factory

```php
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
```

### Campaign Factory

```php
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        
        return [
            'campaign_category_id' => CampaignCategory::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->paragraphs(3, true),
            'image' => 'campaigns/' . fake()->uuid() . '.jpg',
            'target_amount' => fake()->numberBetween(10000000, 100000000),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+3 months'),
            'status' => Campaign::STATUS_ACTIVE,
            'is_featured' => fake()->boolean(20),
        ];
    }
}
```

### Donation Factory

```php
class DonationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'user_id' => User::factory(),
            'amount' => fake()->randomElement([10000, 25000, 50000, 100000, 250000, 500000]),
            'payment_method' => 'midtrans',
            'status' => fake()->randomElement([
                Donation::STATUS_PENDING,
                Donation::STATUS_PAID,
                Donation::STATUS_FAILED,
            ]),
            'is_anonymous' => fake()->boolean(10),
            'message' => fake()->optional()->sentence(),
            'order_id' => 'ORD-' . time() . '-' . strtoupper(Str::random(6)),
        ];
    }
    
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Donation::STATUS_PAID,
            'paid_at' => now(),
        ]);
    }
    
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Donation::STATUS_PENDING,
            'paid_at' => null,
        ]);
    }
}
```

## Seeders

### Database Seeder

```php
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,           // Roles & permissions
            UserSeeder::class,             // Sample users
            CampaignCategorySeeder::class, // Categories
            CampaignSeeder::class,         // Sample campaigns
            DonationSeeder::class,         // Sample donations
        ]);
    }
}
```

### Running Seeders

```bash
# Seed all
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=CampaignSeeder

# Fresh database with seed
php artisan migrate:fresh --seed
```

## Development Workflow

### Local Development

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start servers
php artisan serve
npm run dev
```

### Code Style

```bash
# Fix PHP code style
./vendor/bin/pint

# Fix with specific preset
./vendor/bin/pint --preset=laravel

# Check only (don't fix)
./vendor/bin/pint --test
```

### Static Analysis

```bash
# Run PHPStan
./vendor/bin/phpstan analyse

# With specific level
./vendor/bin/phpstan analyse --level=8
```

## Debugging

### Logging

```php
// Simple log
Log::info('User donated', ['user_id' => $userId, 'amount' => $amount]);

// Debug log
Log::debug('Donation data', $donation->toArray());

// Error log
Log::error('Payment failed', ['error' => $e->getMessage()]);
```

### Dump and Die

```php
// Simple dump
dd($variable);

// Dump with label
dump('User:', $user);

// Query log
DB::enableQueryLog();
// ... run queries
dd(DB::getQueryLog());
```

### Telescope (if installed)

```bash
# Access telescope
http://localhost:8000/telescope

# Install
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

## Deployment Checklist

### Pre-deployment

- [ ] All tests passing
- [ ] Code style fixed (`pint`)
- [ ] Static analysis passing (`phpstan`)
- [ ] Assets built (`npm run build`)
- [ ] Environment variables set
- [ ] Database migrated
- [ ] Storage linked (`php artisan storage:link`)

### Post-deployment

- [ ] Cache config (`php artisan config:cache`)
- [ ] Cache routes (`php artisan route:cache`)
- [ ] Cache views (`php artisan view:cache`)
- [ ] Optimize (`php artisan optimize`)
- [ ] Test critical paths
- [ ] Monitor error logs

### Production Commands

```bash
# Optimize for production
php artisan optimize

# Clear all caches
php artisan optimize:clear

# Storage link
php artisan storage:link

# Queue worker (if using queues)
php artisan queue:work

# Schedule runner
php artisan schedule:run
```
