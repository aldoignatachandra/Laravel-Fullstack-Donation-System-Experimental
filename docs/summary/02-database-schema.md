# 02 - Database Schema & ERD

## Entity Relationship Overview

### Core Entities

```
users (1) ----< (N) campaigns
  |                  |
  |                  |
  |              (N) >---- (1) campaign_categories
  |
  ----< (N) donations
            |
            |
        (N) >---- (1) campaigns
```

## Table Definitions

### 1. users
Store user accounts for both donors and admins

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| name | string | User's full name |
| email | string (unique) | User's email address |
| password | string | Hashed password (bcrypt) |
| email_verified_at | timestamp | When email was verified |
| remember_token | string | For "remember me" functionality |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Relationships:**
- `hasMany` Campaigns
- `hasMany` Donations  
- `hasMany` CampaignArticles
- `morphToMany` Roles

---

### 2. campaign_categories
Categorize campaigns by type

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| name | string | Category name (e.g., "Pendidikan", "Kesehatan") |
| description | text | Category description |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Default Categories:**
1. Pendidikan (Education)
2. Kesehatan (Health)
3. Bencana Alam (Natural Disaster)
4. Sosial (Social)
5. Infrastruktur (Infrastructure)

---

### 3. campaigns
Fundraising campaigns

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| campaign_category_id | bigint (FK) | Reference to category |
| user_id | bigint (FK) | Campaign owner |
| image | string | Cover image path |
| title | string | Campaign title |
| description | text | Full description |
| slug | string (unique) | URL-friendly identifier |
| target_amount | decimal | Fundraising goal (IDR) |
| start_date | date | Campaign start date |
| end_date | date | Campaign end date |
| status | tinyint | Campaign status |
| is_featured | boolean | Featured on homepage |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Status Constants:**
```php
const STATUS_DRAFT = 0;      // Not published
const STATUS_ACTIVE = 1;     // Accepting donations
const STATUS_PAUSED = 2;     // Temporarily stopped
const STATUS_COMPLETED = 3;  // Target reached or ended
const STATUS_CANCELLED = 4;  // Cancelled by owner/admin
```

---

### 4. donations
Donation transactions

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| campaign_id | bigint (FK) | Reference to campaign |
| user_id | bigint (FK) | Donor user |
| amount | decimal | Donation amount (IDR) |
| payment_method | string | Payment method used |
| status | tinyint | Payment status |
| is_anonymous | boolean | Hide donor name |
| message | text | Donor message |
| order_id | string (unique) | Midtrans order identifier |
| payment_type | string | Midtrans payment type |
| paid_at | timestamp | When payment was completed |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Status Constants:**
```php
const STATUS_PENDING = 0;    // Waiting for payment
const STATUS_PAID = 1;       // Payment successful
const STATUS_FAILED = 2;     // Payment failed
const STATUS_CANCELLED = 3;  // Payment cancelled
```

---

### 5. campaign_articles
News/updates for campaigns

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| campaign_id | bigint (FK) | Reference to campaign |
| author_id | bigint (FK) | Article author |
| title | string | Article title |
| slug | string (unique) | URL-friendly identifier |
| content | text | Article content |
| status | string | Article status |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

**Status Values:**
- `draft` - Not published
- `published` - Publicly visible
- `archived` - No longer shown

---

### 6. attachments
Polymorphic file attachments

| Column | Type | Description |
|--------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| attachable_id | bigint | Parent model ID |
| attachable_type | string | Parent model class |
| color | string | Color code |
| path | string | File storage path |
| mime_type | string | MIME type |
| file_name | string | Original filename |
| file_type | string | File type category |
| file_size | int | File size in bytes |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Last update time |

---

## Spatie Permission Tables

- `permissions` - Available permissions
- `roles` - User roles (super_admin, donor)
- `model_has_permissions` - Direct user permissions
- `model_has_roles` - User role assignments
- `role_has_permissions` - Permissions per role

## Indexes

| Table | Column | Type | Purpose |
|-------|--------|------|---------|
| users | email | unique | Login lookups |
| campaigns | slug | unique | URL routing |
| campaigns | user_id | index | User's campaigns |
| campaigns | campaign_category_id | index | Category filtering |
| donations | order_id | unique | Midtrans lookups |
| donations | campaign_id | index | Campaign donations |
| donations | user_id | index | User history |
| campaign_articles | slug | unique | Article URLs |
| campaign_articles | campaign_id | index | Campaign articles |

## Relationship Summary

```
User
├── hasMany Campaign
├── hasMany Donation
├── hasMany CampaignArticle
└── morphToMany Role

CampaignCategory
└── hasMany Campaign

Campaign
├── belongsTo User
├── belongsTo CampaignCategory
├── hasMany Donation
├── hasMany CampaignArticle
└── morphMany Attachment

Donation
├── belongsTo Campaign
└── belongsTo User

CampaignArticle
├── belongsTo Campaign
└── belongsTo User (author)

Attachment
└── morphTo (Campaign, etc.)
```
