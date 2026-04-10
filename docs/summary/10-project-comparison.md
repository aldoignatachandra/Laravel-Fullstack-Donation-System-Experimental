# 10 - Project Comparison: Tedja vs DonasiKita

## Overview

Both projects are Laravel-based fullstack applications, but serve different purposes and demonstrate different architectural patterns.

## Side-by-Side Comparison

| Aspect | Tedja Project | DonasiKita Project |
|--------|--------------|-------------------|
| **Purpose** | Property listing & KPR mortgage platform | Crowdfunding & donation platform |
| **Industry** | Real Estate / Finance | Social / Non-profit |
| **Domain** | Indonesian property market | Fundraising campaigns |

## Technical Comparison

### Framework & Versions

| Aspect | Tedja | DonasiKita |
|--------|-------|------------|
| **Laravel** | 12.x | 12.x |
| **PHP** | 8.2+ | 8.2+ |
| **Database** | MySQL | SQLite (default) |
| **Frontend** | Blade + Tailwind | Livewire + Flux + Tailwind |

### Key Packages

| Package | Tedja | DonasiKita |
|---------|-------|------------|
| **Admin Panel** | Filament 3.3 | Filament 4.0 |
| **Permissions** | Spatie Permission 6.20 | Spatie Permission 6.21 + Shield |
| **Payment** | Midtrans 2.6 | Midtrans 2.6 |
| **Auth Scaffolding** | Laravel Breeze 2.4 | Laravel Breeze (latest) |
| **Livewire** | Not used | Livewire 3.x + Volt |
| **UI Library** | Custom Blade components | Flux UI 2.1 |

## Architecture Differences

### Tedja: Traditional MVC

```
Request → Controller → Service → Model → Database
                ↓
            View (Blade)
```

**Characteristics:**
- Classic MVC pattern
- Controller handles HTTP layer
- Service layer for business logic
- Blade views with minimal JavaScript
- Server-side rendered pages
- Form submissions via POST requests

### DonasiKita: Modern Livewire

```
Request → Livewire Component → Service → Model → Database
                ↓
        Reactive UI (Livewire + Blade)
```

**Characteristics:**
- Reactive components with Livewire
- Real-time updates without page refresh
- Single-file components (Volt)
- Component-based architecture
- Server-side state management
- Alpine.js for client-side interactions

## Feature Comparison

### User Roles

| Tedja | DonasiKita |
|-------|-----------|
| admin | super_admin |
| customer | donor |
| lender (reserved) | - |
| agent (reserved) | - |

### Core Features

| Feature | Tedja | DonasiKita |
|---------|-------|------------|
| **Listings/Campaigns** | Property listings | Fundraising campaigns |
| **Categories** | House categories | Campaign categories |
| **Payments** | Installment payments | One-time donations |
| **Calculations** | Mortgage amortization | Progress tracking |
| **Search** | City + Category | Title + Category |
| **Gallery** | House photos | Campaign articles |
| **Documents** | KPR documents | Attachments |

### Admin Features

| Feature | Tedja | DonasiKita |
|---------|-------|------------|
| **Resources** | House, Bank, Category, City, Mortgage | Campaign, Category, Donation, User |
| **Widgets** | Custom stats | Stats + Charts |
| **Relations** | House photos, facilities | Donations (via relation manager) |
| **Processing** | Approve/reject mortgages | View/manage donations |

## Database Comparison

### Tedja Tables

| Table | Purpose |
|-------|---------|
| users | Customers & admins |
| houses | Property listings |
| categories | House categories |
| cities | Location cities |
| banks | Mortgage providers |
| interests | Bank interest rates |
| mortgage_requests | KPR applications |
| installments | Payment records |
| house_photos | Gallery images |
| house_facilities | Property amenities |
| facilities | Amenity types |

### DonasiKita Tables

| Table | Purpose |
|-------|---------|
| users | Donors & admins |
| campaigns | Fundraising campaigns |
| campaign_categories | Campaign types |
| donations | Donation transactions |
| campaign_articles | Updates/news |
| attachments | File uploads |

## Code Structure Comparison

### Controllers vs Livewire

**Tedja (Controllers):**
```php
class FrontController extends Controller
{
    public function index()
    {
        $houses = House::all();
        return view('front.index', compact('houses'));
    }
}
```

**DonasiKita (Livewire):**
```php
class LandingPage extends Component
{
    public function render()
    {
        return view('livewire.landing.landing-page', [
            'campaigns' => Campaign::all(),
        ]);
    }
}
```

### Service Layer

Both use Service Layer pattern similarly:

**Tedja:**
- `MortgageService` - KPR calculations
- `PaymentService` - Payment processing
- `MidtransService` - Gateway integration
- `HouseService` - House queries

**DonasiKita:**
- `DonationService` - All donation logic (single comprehensive service)

## UI/UX Differences

### Tedja
- Traditional multi-page application
- Form submissions reload page
- Progress indicators on mortgage calculator
- Property gallery with lightbox
- Static pages with minimal interactivity

### DonasiKita
- Single-page application feel
- Real-time search filtering
- Live donation progress updates
- Modal-based article viewing
- Reactive form validation
- Smooth transitions

## Payment Flow Comparison

### Tedja: Installment System

```
1. User applies for KPR
2. Admin approves application
3. System generates installment schedule
4. User pays monthly via Midtrans
5. Payment recorded as installment
6. Track remaining loan balance
```

**Payment Structure:**
- Sub Total: Monthly payment amount
- Tax (11%): PPN/VAT
- Insurance: Fixed Rp 900,000
- Grand Total: Sum of all

### DonasiKita: One-time Donation

```
1. User selects campaign
2. Enters donation amount
3. Redirected to Midtrans
4. Completes payment
5. Webhook updates status
6. Notifications sent
```

**Payment Structure:**
- Simple one-time amount
- No complex calculations
- Anonymous option
- Optional message

## Learning Outcomes

### From Tedja
1. **Service Layer Pattern** - Clean separation of concerns
2. **Complex Calculations** - Mortgage amortization formula
3. **Multi-step Workflows** - KPR application process
4. **Document Handling** - File uploads for KPR
5. **Traditional Laravel** - Classic MVC structure

### From DonasiKita
1. **Livewire Framework** - Reactive PHP components
2. **Volt Single-file** - Component organization
3. **Filament Shield** - Permission management
4. **Webhook Handling** - Payment gateway integration
5. **Polymorphic Relations** - Flexible attachments
6. **Flux UI** - Modern UI component library

## When to Use Which Approach

### Use Tedja's Approach (Traditional MVC) When:
- SEO is critical (server-side rendering)
- Complex server-side calculations needed
- Traditional form submissions work fine
- Less real-time interactivity required
- Team prefers classic Laravel patterns

### Use DonasiKita's Approach (Livewire) When:
- Real-time updates needed
- Rich user interactions required
- SPA-like experience desired
- Rapid prototyping needed
- Team comfortable with reactive patterns

## Common Patterns in Both

Both projects demonstrate:

1. **Service Layer** - Business logic abstraction
2. **Repository Pattern** - Eloquent models
3. **Role-based Access** - Spatie Permission
4. **Payment Integration** - Midtrans
5. **Database Transactions** - Data consistency
6. **Soft Deletes** - Data safety
7. **Factory Pattern** - Test data generation
8. **Seeders** - Database seeding

## Conclusion

Both projects showcase professional Laravel development:

- **Tedja** excels in traditional, calculation-heavy domains
- **DonasiKita** excels in modern, interactive user experiences

The choice between approaches depends on project requirements, team expertise, and user experience goals.

## Recommendation for Learning

1. **Start with Tedja** if you're new to Laravel
   - Clear MVC separation
   - Easier to understand flow
   - Traditional patterns

2. **Study DonasiKita** for modern Laravel
   - Current best practices
   - Reactive programming
   - Modern UI patterns

Both projects together provide a comprehensive understanding of Laravel fullstack development from traditional to modern approaches.
