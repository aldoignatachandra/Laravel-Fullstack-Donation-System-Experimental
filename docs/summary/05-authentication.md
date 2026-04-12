# 05 - Authentication & Authorization

## Overview

DonasiKita uses Laravel's built-in authentication combined with Spatie Laravel Permission for role-based access control.

## Authentication System

### Provider
- **Driver**: `session`
- **Guard**: `web`
- **Model**: `App\Models\User`

### Login Flow

```
1. User visits /login
   -> Volt component renders login form
   -> User submits email + password
   -> Validation: email exists, password correct
   -> Rate limiting: max 5 attempts per minute
   -> Session created, user redirected to /dashboard
```

### Registration Flow

```
1. User visits /register
   -> Volt component renders registration form
   -> User submits name, email, password, password_confirmation
   -> Validation:
      - name: required, string, max:255
      - email: required, email, unique:users
      - password: required, confirmed, min:8
   -> User created with hashed password
   -> Event: Registered fired
   -> Listener: AfterUserRegistered assigns 'donor' role
   -> User logged in, redirected to /dashboard
```

### Email Verification

```
1. After registration, user sees /verify-email
   -> Email sent with signed URL
   -> User clicks link: /verify-email/{id}/{hash}
   -> Email marked as verified
   -> User can now access dashboard
```

### Password Reset

```
1. User clicks "Forgot password"
   -> Enters email on /forgot-password
   -> Reset link sent via email
   -> User clicks link: /reset-password/{token}
   -> Enters new password
   -> Password updated, user redirected to login
```

## Authorization System

### Roles

| Role | Description | Created By |
|------|-------------|------------|
| `super_admin` | Full system access | ShieldSeeder |
| `donor` | Regular donor user | AfterUserRegistered listener |

### Role Assignment

```php
// When user registers
class AfterUserRegistered
{
    public function handle(Registered $event): void
    {
        $event->user->assignRole(User::ROLE_DONOR);
    }
}
```

```php
// Super admin seeder
class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::create(['name' => 'super_admin']);
        
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('example'),
        ]);
        
        $user->assignRole('super_admin');
    }
}
```

### Permissions (Shield Generated)

Shield automatically generates CRUD permissions:

```
ViewAny:{Resource}
View:{Resource}
Create:{Resource}
Update:{Resource}
Delete:{Resource}
Restore:{Resource}
ForceDelete:{Resource}
ForceDeleteAny:{Resource}
RestoreAny:{Resource}
Replicate:{Resource}
Reorder:{Resource}
```

Resources with permissions:
- Campaign
- CampaignCategory
- Donation
- User
- Role

### Middleware

```php
// Registered in bootstrap/app.php
'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
```

### Route Protection

```php
// Public routes - no protection
Route::get('/', LandingPage::class);
Route::get('/campaign/{slug}', ShowCampaign::class);

// Auth required
Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified']);

Route::get('/campaign/{slug}/donate', DonationForm::class)
    ->middleware(['auth', 'verified']);

// Admin panel (Filament)
// Protected by Filament's built-in auth + Shield
```

### Policy Authorization

```php
// app/Policies/CampaignPolicy.php
class CampaignPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Campaign');
    }
    
    public function view(User $user, Campaign $campaign): bool
    {
        return $user->can('View:Campaign');
    }
    
    public function create(User $user): bool
    {
        return $user->can('Create:Campaign');
    }
    
    // ... other methods
}
```

### Checking Permissions

```php
// In blade
@can('Create:Campaign')
    <a href="{{ route('filament.admin.resources.campaigns.create') }}">
        Create Campaign
    </a>
@endcan

// In controller
if ($user->can('Update:Campaign')) {
    // allow update
}

// Check role
if ($user->hasRole('super_admin')) {
    // admin only
}

// In Livewire
$this->authorize('Create:Campaign');
```

## Super Admin Access

### Filament Panel Access

```php
// app/Providers/Filament/AdminPanelProvider.php
public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('admin')
        ->path('admin')
        ->login()
        ->authGuard('web')
        ->middleware([
            'auth',
        ])
        ->plugins([
            FilamentShieldPlugin::make(),
        ]);
}
```

Shield checks `super_admin` role for admin panel access.

### Default Admin Credentials

```
URL: /admin
Email: superadmin@example.com
Password: example
```

## Security Best Practices

### 1. Password Requirements
- Minimum 8 characters
- Must be confirmed (password_confirmation)
- Hashed with bcrypt

### 2. Rate Limiting
```php
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

### 3. Session Security
```php
// config/session.php
'encrypt' => true,
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'lax',
```

### 4. CSRF Protection
All forms include `@csrf` directive.

### 5. Email Verification
Required for dashboard access:
```php
Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified']);
```

## User Model

```php
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;
    
    const ROLE_DONOR = 'donor';
    const ROLE_SUPER_ADMIN = 'super_admin';
    
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // Filament access control
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }
    
    // Relationships
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
    
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
    
    public function articles(): HasMany
    {
        return $this->hasMany(CampaignArticle::class, 'author_id');
    }
}
```

## Testing Authentication

```php
class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }
    
    public function test_users_can_authenticate()
    {
        $user = User::factory()->create();
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
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
