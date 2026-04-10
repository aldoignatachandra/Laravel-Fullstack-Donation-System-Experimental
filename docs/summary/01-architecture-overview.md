# 01 - Architecture Overview

## System Architecture

DonasiKita uses a modern Laravel architecture with Livewire for reactive frontend components.

## Architecture Layers

### 1. Presentation Layer
- **Public Pages**: Landing page, campaign details, donation form
- **User Dashboard**: Statistics, donation history, settings
- **Admin Panel**: Filament-based CRUD management

### 2. Application Layer
- **Livewire Components**: Reactive UI components
- **HTTP Controllers**: Handle webhooks and non-Livewire routes
- **Service Layer**: Business logic encapsulation

### 3. Data Layer
- **Models**: Eloquent ORM for database access
- **Query Scopes**: Reusable query constraints
- **Middleware**: Authentication and authorization

### 4. External Services
- **Midtrans**: Payment gateway integration
- **Mail**: Email notifications
- **Queue**: Background job processing

## Design Patterns

### 1. Service Layer Pattern
- **Location**: `app/Services/DonationService.php`
- **Purpose**: Encapsulate business logic
- **Benefits**: Reusability, testability

### 2. Repository Pattern (Implicit)
- **Location**: Eloquent Models
- **Purpose**: Abstract database access

### 3. Policy Pattern
- **Location**: `app/Policies/`
- **Purpose**: Authorization logic

### 4. Observer Pattern
- **Location**: `app/Listeners/`
- **Purpose**: React to events

### 5. Factory Pattern
- **Location**: `database/factories/`
- **Purpose**: Create test data

## Request Flow

```
1. Browser Request
   ↓
2. Laravel Router
   ↓
3. Middleware Stack
   ↓
4. Livewire/Controller
   ↓
5. Service Layer
   ↓
6. Model Layer
   ↓
7. Database
   ↓
8. Response
```

## File Organization

```
app/
├── Filament/           # Admin panel
├── Helper/             # Utility functions
├── Http/
│   ├── Controllers/    # HTTP controllers
│   └── Middleware/     # Custom middleware
├── Livewire/           # UI components
├── Models/             # Eloquent models
├── Notifications/      # Email templates
├── Policies/           # Authorization
└── Services/           # Business logic
```
