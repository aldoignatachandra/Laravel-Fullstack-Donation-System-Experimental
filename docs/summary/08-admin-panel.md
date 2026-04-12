# 08 - Admin Panel (Filament)

## Overview

DonasiKita uses Filament v4 with Shield for a comprehensive admin panel. The admin panel provides full CRUD management for all entities.

## Access

- **URL**: `/admin`
- **Default Credentials**:
  - Email: `superadmin@example.com`
  - Password: `example`

## Configuration

### Provider Setup

**File**: `app/Providers/Filament/AdminPanelProvider.php`

```php
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ]);
    }
}
```

## Resources

### 1. CampaignResource

**File**: `app/Filament/Resources/Campaigns/CampaignResource.php`

**Navigation**: Campaign Management > Campaigns  
**Icon**: BellAlert  
**Permission**: ViewAny:Campaign

**Pages**:
- List Campaigns
- Create Campaign
- View Campaign
- Edit Campaign

**Form Fields**:
```php
public static function form(Form $form): Form
{
    return $form->schema([
        // Basic Info Section
        Forms\Components\Section::make('Basic Information')
            ->schema([
                Forms\Components\Select::make('campaign_category_id')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(5),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->required(),
            ]),
        
        // Campaign Details Section
        Forms\Components\Section::make('Campaign Details')
            ->schema([
                Forms\Components\TextInput::make('target_amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured on Homepage'),
            ]),
        
        // Status Section
        Forms\Components\Section::make('Status')
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        0 => 'Draft',
                        1 => 'Active',
                        2 => 'Paused',
                        3 => 'Completed',
                        4 => 'Cancelled',
                    ])
                    ->required(),
            ]),
    ]);
}
```

**Table Columns**:
```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\ImageColumn::make('image')
                ->size(50),
            Tables\Columns\TextColumn::make('title')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('category.name')
                ->badge(),
            Tables\Columns\TextColumn::make('target_amount')
                ->money('IDR')
                ->sortable(),
            Tables\Columns\TextColumn::make('start_date')
                ->date()
                ->sortable(),
            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 0,  // Draft
                    'success' => 1,  // Active
                    'danger' => 2,   // Paused
                    'primary' => 3,  // Completed
                    'secondary' => 4, // Cancelled
                ])
                ->formatStateUsing(fn ($state) => match($state) {
                    0 => 'Draft',
                    1 => 'Active',
                    2 => 'Paused',
                    3 => 'Completed',
                    4 => 'Cancelled',
                }),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    0 => 'Draft',
                    1 => 'Active',
                    2 => 'Paused',
                    3 => 'Completed',
                    4 => 'Cancelled',
                ]),
            Tables\Filters\SelectFilter::make('campaign_category_id')
                ->relationship('category', 'name'),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}
```

**Relations**:
```php
public static function getRelations(): array
{
    return [
        RelationManagers\DonationsRelationManager::class,
    ];
}
```

---

### 2. UserResource

**File**: `app/Filament/Resources/Users/UserResource.php`

**Navigation**: User Management > Users  
**Icon**: Users  
**Permission**: ViewAny:User

**Features**:
- Create/edit users
- Assign roles
- View user donations

---

### 3. DonationResource

**File**: `app/Filament/Resources/Donations/DonationResource.php`

**Navigation**: Campaign Management > Donations  
**Icon**: CurrencyDollar  
**Permission**: ViewAny:Donation

**Features**:
- View all donations
- Filter by status
- Export data
- View donation details

---

### 4. CampaignCategoryResource

**File**: `app/Filament/Resources/CampaignCategories/CampaignCategoryResource.php`

**Navigation**: Campaign Management > Categories  
**Icon**: BarsArrowUp  
**Permission**: ViewAny:CampaignCategory

**Features**:
- Manage campaign categories
- View campaigns in category

---

### 5. RoleResource (Shield)

**Navigation**: User Management > Roles  
**Icon**: ShieldCheck  

**Features**:
- Create custom roles
- Assign permissions
- Manage role access

## Widgets

### 1. CampaignStatusStats

**File**: `app/Filament/Widgets/CampaignStatusStats.php`

Displays campaign counts by status:
- Total Campaigns
- Draft
- Active
- Paused
- Completed
- Cancelled

```php
class CampaignStatusStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total', Campaign::count()),
            Stat::make('Active', Campaign::where('status', 1)->count()),
            Stat::make('Completed', Campaign::where('status', 3)->count()),
        ];
    }
}
```

### 2. DonationStatusStats

**File**: `app/Filament/Widgets/DonationStatusStats.php`

Displays donation totals by status:
- Total Amount
- Pending Amount
- Paid Amount
- Failed Amount
- Cancelled Amount

### 3. DonationsCollectedBarChart

**File**: `app/Filament/Widgets/DonationsCollectedBarChart.php`

Monthly donation bar chart showing:
- Total donations per month
- Only paid donations
- Filterable by date range

```php
class DonationsCollectedBarChart extends ChartWidget
{
    protected static ?string $heading = 'Donations Collected';
    
    protected function getData(): array
    {
        $data = Donation::where('status', Donation::STATUS_PAID)
            ->selectRaw('SUM(amount) as total, MONTH(created_at) as month')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');
        
        return [
            'datasets' => [
                [
                    'label' => 'Donations (IDR)',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys()->map(fn ($m) => DateTime::createFromFormat('!m', $m)->format('F')),
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
}
```

## Shield Configuration

### Config File

**File**: `config/filament-shield.php`

```php
return [
    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
    ],
    'panel_user' => [
        'enabled' => true,
        'name' => 'panel_user',
    ],
    'permission_prefixes' => [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force_delete',
    ],
];
```

### Generating Permissions

```bash
# Generate permissions for all resources
php artisan shield:generate --all

# Generate for specific resource
php artisan shield:generate --resource=CampaignResource
```

## Custom Pages

### Creating Custom Page

```php
php artisan make:filament-page Settings Admin
```

**Example file (if generated)**: `app/Filament/Pages/Settings.php`

```php
class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static string $view = 'filament.pages.settings';
    
    protected static ?string $navigationGroup = 'Configuration';
    
    protected static ?int $navigationSort = 99;
}
```

## Actions

### Custom Actions

```php
// In table
Tables\Actions\Action::make('approve')
    ->label('Approve')
    ->icon('heroicon-o-check')
    ->color('success')
    ->requiresConfirmation()
    ->action(function (Campaign $record) {
        $record->update(['status' => Campaign::STATUS_ACTIVE]);
    })
    ->visible(fn (Campaign $record) => $record->status === Campaign::STATUS_DRAFT);
```

### Bulk Actions

```php
Tables\Actions\BulkAction::make('approveSelected')
    ->label('Approve Selected')
    ->icon('heroicon-o-check')
    ->color('success')
    ->action(function (Collection $records) {
        $records->each->update(['status' => Campaign::STATUS_ACTIVE]);
    });
```

## Notifications

### Sending Notifications

```php
use Filament\Notifications\Notification;

Notification::make()
    ->title('Campaign approved')
    ->body('The campaign has been approved and is now active.')
    ->success()
    ->send();
```

### Database Notifications

```php
Notification::make()
    ->title('New Donation')
    ->body('You received a new donation of Rp 100,000')
    ->success()
    ->sendToDatabase($user);
```

## Best Practices

### 1. Resource Organization
- Group related resources
- Use consistent icons
- Set navigation order

### 2. Forms
- Use sections to organize fields
- Add validation rules
- Use appropriate input types

### 3. Tables
- Enable search for text fields
- Add filters for status/relationships
- Use actions for common operations

### 4. Security
- Always check permissions
- Use policies for authorization
- Validate all inputs

### 5. Performance
- Use pagination
- Add indexes for searchable fields
- Cache expensive operations
