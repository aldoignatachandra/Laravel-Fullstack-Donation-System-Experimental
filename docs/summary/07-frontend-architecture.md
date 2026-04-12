# 07 - Frontend Architecture

## Overview

DonasiKita uses a modern frontend stack combining Laravel Blade, Livewire, and Tailwind CSS for a reactive, server-rendered user interface.

## Technology Stack

| Technology | Purpose | Version |
|------------|---------|---------|
| Livewire | Reactive components | 3.x |
| Livewire Volt | Single-file components | 1.7.x |
| Flux UI | UI component library | 2.3.x |
| Tailwind CSS | Utility-first CSS | 4.x |
| Alpine.js | JavaScript interactions | Included with Livewire |
| Vite | Build tool | Latest |

## Directory Structure

```
resources/
├── css/
│   └── app.css              # Tailwind imports
├── js/
│   └── app.js               # App initialization
└── views/
    ├── components/          # Blade components
    │   ├── layouts/         # Layout templates
    │   │   ├── beramal.blade.php    # Public layout
    │   │   ├── app.blade.php        # Dashboard layout
    │   │   └── auth.blade.php       # Auth layout
    │   └── ui/              # UI components
    ├── livewire/            # Livewire component views
    │   ├── landing/
    │   │   ├── landing-page.blade.php
    │   │   ├── _hero.blade.php
    │   │   ├── _category-filter.blade.php
    │   │   ├── _campaigns.blade.php
    │   │   ├── _how-it-works.blade.php
    │   │   └── _footer.blade.php
    │   ├── campaign/
    │   │   ├── show-campaign.blade.php
    │   │   └── donation-form.blade.php
    │   └── dashboard/
    │       ├── dashboard.blade.php
    │       └── donations.blade.php
    ├── auth/                # Auth pages
    └── dashboard/           # Dashboard pages
```

## Layouts

### 1. Public Layout (beramal)

**File**: `resources/views/components/layouts/beramal.blade.php`

**Usage**: Public pages (landing, campaign details)

```html
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DonasiKita')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <x-navbar />
    
    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Footer -->
    <x-footer />
    
    @livewireScripts
</body>
</html>
```

### 2. App Layout

**File**: `resources/views/components/layouts/app.blade.php`

**Usage**: Authenticated dashboard pages

Features:
- Sidebar navigation
- User menu
- Responsive design
- Dark mode support

### 3. Auth Layout

**File**: `resources/views/components/layouts/auth.blade.php`

**Usage**: Login, register, password reset pages

## Livewire Components

### LandingPage Component

**Class**: `app/Livewire/LandingPage.php`
**View**: `resources/views/livewire/landing/landing-page.blade.php`

**Features**:
- Real-time search
- Category filtering
- Pagination

```php
class LandingPage extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedCategory = '';
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function render()
    {
        $campaigns = Campaign::query()
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            })
            ->paginate(12);
            
        return view('livewire.landing.landing-page', [
            'campaigns' => $campaigns,
        ]);
    }
}
```

### ShowCampaign Component

**Class**: `app/Livewire/Campaign/ShowCampaign.php`
**View**: `resources/views/livewire/campaign/show-campaign.blade.php`

**Features**:
- Campaign details display
- Article modal
- Recent donors list

### DonationForm Component

**Class**: `app/Livewire/Campaign/DonationForm.php`
**View**: `resources/views/livewire/campaign/donation-form.blade.php`

**Features**:
- Amount selection (preset or custom)
- Message input
- Anonymous checkbox
- Form validation

## Blade Components

### Custom Components

```php
// x-navbar
<x-navbar />

// x-campaign-card
<x-campaign-card :campaign="$campaign" />

// x-progress-bar
<x-progress-bar :percent="75" />
```

### Flux UI Components

```html
<!-- Button -->
<flux:button variant="primary">Donate Now</flux:button>

<!-- Input -->
<flux:input wire:model="amount" label="Amount" prefix="Rp" />

<!-- Card -->
<flux:card>
    <flux:heading>Campaign Title</flux:heading>
    <flux:text>Description here</flux:text>
</flux:card>

<!-- Modal -->
<flux:modal wire:model="showModal">
    <flux:heading>Modal Title</flux:heading>
    <flux:text>Modal content</flux:text>
</flux:modal>
```

## Styling with Tailwind CSS

### CSS-first Configuration (Tailwind v4)

```css
/* resources/css/app.css */
@import 'tailwindcss';

@source '../views';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
}
```

```js
// vite.config.js
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel(['resources/css/app.css', 'resources/js/app.js']),
        tailwindcss(),
    ],
});
```

### Common Patterns

```html
<!-- Campaign Card -->
<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
    <img src="{{ $campaign->image }}" class="w-full h-48 object-cover">
    <div class="p-4">
        <span class="text-xs font-semibold text-primary-600 bg-primary-50 px-2 py-1 rounded">
            {{ $campaign->category->name }}
        </span>
        <h3 class="mt-2 text-lg font-semibold text-gray-900">
            {{ $campaign->title }}
        </h3>
        <!-- Progress bar -->
        <div class="mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
            <div class="mt-2 flex justify-between text-sm text-gray-600">
                <span>Rp {{ number_format($collected) }}</span>
                <span>{{ $progress }}%</span>
            </div>
        </div>
    </div>
</div>
```

## Responsive Design

### Breakpoints

| Breakpoint | Min Width | Usage |
|------------|-----------|-------|
| sm | 640px | Mobile landscape |
| md | 768px | Tablets |
| lg | 1024px | Laptops |
| xl | 1280px | Desktops |
| 2xl | 1536px | Large screens |

### Responsive Grid

```html
<!-- Campaign grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($campaigns as $campaign)
        <x-campaign-card :campaign="$campaign" />
    @endforeach
</div>
```

### Responsive Layout

```html
<!-- Campaign detail page -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main content (2/3 on large screens) -->
    <div class="lg:col-span-2">
        <img src="{{ $campaign->image }}">
        <h1>{{ $campaign->title }}</h1>
        <p>{{ $campaign->description }}</p>
    </div>
    
    <!-- Sidebar (1/3 on large screens) -->
    <div class="lg:sticky lg:top-4 lg:h-fit">
        <x-donation-card :campaign="$campaign" />
    </div>
</div>
```

## Interactive Features

### Real-time Search

```php
// Component
class LandingPage extends Component
{
    public $search = '';
    
    public function updatedSearch()
    {
        // Automatically re-renders with new results
        $this->resetPage();
    }
}
```

```html
<!-- View -->
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="Search campaigns..."
    class="w-full px-4 py-2 border rounded-lg"
>
```

### Loading States

```html
<div wire:loading>
    <x-loading-spinner />
</div>

<div wire:loading.remove>
    <!-- Content -->
</div>
```

### Confirm Actions

```php
public function deleteCampaign($id)
{
    // Show confirmation dialog
    $this->dispatch('confirm-delete', id: $id);
}

#[On('delete-confirmed')]
public function confirmedDelete($id)
{
    Campaign::find($id)->delete();
}
```

## Asset Building

### Vite Configuration

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
```

### Development

```bash
# Run Vite dev server
npm run dev
```

### Production Build

```bash
# Build for production
npm run build
```

## Best Practices

### 1. Component Organization
- One component per file
- Keep components small and focused
- Use sub-components for reuse

### 2. State Management
- Use public properties for component state
- Avoid passing large objects to Livewire
- Use computed properties for derived data

### 3. Performance
- Use pagination for large lists
- Lazy load images
- Minimize database queries with eager loading
- Use `wire:loading` for better UX

### 4. Accessibility
- Use semantic HTML
- Include alt text for images
- Ensure keyboard navigation
- Use proper heading hierarchy

### 5. Forms
- Always validate on server
- Show clear error messages
- Use proper input types
- Include CSRF tokens
