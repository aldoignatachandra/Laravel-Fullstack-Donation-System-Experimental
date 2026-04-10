# 03a - Data Flow: Campaign Browsing

## Overview
This flow shows how a public visitor browses campaigns on the homepage.

## Step-by-Step Flow

### 1. Initial Request
- **URL**: GET /
- **Route**: `Route::get('/', LandingPage::class)`
- **Component**: `App\Livewire\LandingPage`

### 2. Component Initialization
```php
// LandingPage::mount()
public function mount()
{
    // Load all categories for filter dropdown
    $this->categories = CampaignCategory::all();
}
```

### 3. Data Query (LandingPage::render())
```php
public function render()
{
    // Base query with eager loading
    $campaigns = Campaign::with(['category', 'donations', 'user'])
        ->where('status', Campaign::STATUS_ACTIVE)
        
        // Search filter (if user typed in search box)
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        })
        
        // Category filter (if user selected a category)
        ->when($this->selectedCategory, function ($query) {
            $query->where('campaign_category_id', $this->selectedCategory);
        })
        
        ->orderBy('created_at', 'desc')
        ->paginate(12);
    
    return view('livewire.landing.landing-page', [
        'campaigns' => $campaigns,
        'categories' => $this->categories,
    ]);
}
```

### 4. Database Queries Executed
```sql
-- Query 1: Get categories for filter
SELECT * FROM campaign_categories;

-- Query 2: Get campaigns with pagination
SELECT * FROM campaigns 
WHERE status = 1 
ORDER BY created_at DESC 
LIMIT 12 OFFSET 0;

-- Query 3: Get related categories (eager loaded)
SELECT * FROM campaign_categories 
WHERE id IN (1, 2, 3, ...);

-- Query 4: Get related donations for progress calculation
SELECT * FROM donations 
WHERE campaign_id IN (1, 2, 3, ...) 
AND status = 1;

-- Query 5: Get campaign creators
SELECT * FROM users 
WHERE id IN (1, 2, 3, ...);
```

### 5. Data Transformation
For each campaign, the system calculates:
- **Progress Percentage**: `(total_paid_donations / target_amount) * 100`
- **Amount Collected**: Sum of all donations with status = PAID
- **Donor Count**: Count of unique donors
- **Days Left**: `end_date - today`

### 6. View Rendering
**View File**: `resources/views/livewire/landing/landing-page.blade.php`

Components rendered:
1. **Hero Section** (`_hero.blade.php`)
   - Search input field
   - Call-to-action button

2. **Category Filter** (`_category-filter.blade.php`)
   - List of category buttons
   - Active state highlighting

3. **Campaign Grid** (`_campaigns.blade.php`)
   - 3-column grid of campaign cards
   - Each card shows:
     * Cover image
     * Category badge
     * Title
     * Creator name
     * Progress bar
     * Amount collected / Target
     * Days remaining
     * Donor count

4. **How It Works** (`_how-it-works.blade.php`)
   - Step-by-step explanation

5. **Footer** (`_footer.blade.php`)
   - Links and information

### 7. Response
- **Type**: HTML with Livewire JavaScript
- **Features**:
  - Real-time search (no page refresh)
  - Category filtering
  - Pagination links

## Real-Time Updates

When user types in search box:
```php
public function updatedSearch()
{
    $this->resetPage(); // Reset to page 1
    // Livewire automatically re-renders with new search term
}
```

When user clicks category:
```php
public function updatedSelectedCategory()
{
    $this->resetPage();
    // Re-renders with category filter
}
```

## Performance Optimizations

1. **Eager Loading**: `with(['category', 'donations', 'user'])` prevents N+1 queries
2. **Pagination**: Only 12 campaigns loaded at a time
3. **Query Caching**: Categories could be cached (not currently implemented)
4. **Indexing**: `status` and `campaign_category_id` are indexed

## Data Flow Summary

```
Browser -> Router -> LandingPage Component -> Database
                                          |
                                          -> Campaign Model
                                          -> Category Model  
                                          -> Donation Model
                                          |
Browser <- HTML <- View (Blade) <- Component
```
