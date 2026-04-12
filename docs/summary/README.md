# DonasiKita - Laravel Fullstack Project Summary

## Project Overview

**DonasiKita** is a comprehensive crowdfunding and donation platform built with Laravel 12, inspired by platforms like Kitabisa.com and GoFundMe.

## Table of Contents

1. [Architecture Overview](./01-architecture-overview.md)
2. [Database Schema & ERD](./02-database-schema.md)
3. [Data Flow: Campaign Browsing](./03a-flow-campaign-browsing.md)
4. [Data Flow: Donation Creation](./03b-flow-donation-creation.md)
5. [Data Flow: Payment Callback](./03c-flow-payment-callback.md)
6. [Service Layer Architecture](./04-service-layer.md)
7. [Authentication & Authorization](./05-authentication.md)
8. [Payment Integration (Midtrans)](./06-payment-integration.md)
9. [Frontend Architecture](./07-frontend-architecture.md)
10. [Admin Panel (Filament)](./08-admin-panel.md)
11. [Testing & Development](./09-testing-development.md)

## Quick Summary

### Technology Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Livewire 3 + Volt + Flux UI + Tailwind CSS
- **Database**: SQLite (default), MySQL/PostgreSQL compatible
- **Admin Panel**: Filament 4 with Shield
- **Payment Gateway**: Midtrans (Snap API)
- **Authentication**: Laravel Livewire starter auth (Volt) + Spatie Permission

### Core Features
1. **Campaign Management** - Create, edit, and manage fundraising campaigns
2. **Online Donations** - Secure payment processing with Midtrans
3. **User Dashboard** - Track donation history and statistics
4. **Admin Panel** - Comprehensive management via Filament
5. **Real-time Notifications** - Email notifications for donation events
6. **Campaign Articles** - Updates and news for campaigns
7. **Attachment Management** - File uploads for documentation

### User Roles
| Role | Description |
|------|-------------|
| `super_admin` | Full system administration access |
| `donor` | Regular user who can donate and create campaigns |

---

## Getting Started

### Default Admin Account
```
Email: superadmin@example.com
Password: example
```

### Default Donor Account
```
Email: ahmad.rizki@example.com
Password: password
```

## License

This project is for educational purposes.
