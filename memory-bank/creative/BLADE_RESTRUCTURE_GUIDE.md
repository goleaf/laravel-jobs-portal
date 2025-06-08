# Blade Templates Restructure Guide

## Overview

The blade templates have been restructured into a more organized and maintainable structure with clear separation between backend, frontend, and shared components.

## New Directory Structure

```
resources/views/
├── backend/                    # Backend/Admin views
│   ├── layouts/
│   │   └── app.blade.php      # Main backend layout
│   ├── dashboard/
│   │   └── index.blade.php    # Dashboard page
│   ├── admin/
│   ├── users/
│   ├── jobs/
│   └── settings/
├── frontend/                   # Frontend/Public views
│   ├── layouts/
│   │   └── app.blade.php      # Main frontend layout
│   ├── home/
│   │   └── index.blade.php    # Home page
│   ├── jobs/
│   ├── companies/
│   ├── candidates/
│   ├── auth/
│   └── blog/
└── shared/                     # Shared components
    └── components/
        ├── layout/
        │   └── base.blade.php  # Base layout template
        ├── navigation/
        │   └── breadcrumbs.blade.php
        ├── forms/
        │   ├── input.blade.php
        │   ├── button.blade.php
        │   └── select.blade.php
        ├── table/
        │   └── index.blade.php
        └── flash-messages.blade.php
```

## Shared Components

### Base Layout Component
**File:** `resources/views/shared/components/layout/base.blade.php`

A universal base layout that provides:
- HTML structure
- Meta tags (SEO, Open Graph)
- Scripts and styles loading
- Language and direction support
- Flash messages integration

**Usage:**
```blade
<x-shared.components.layout.base 
    :title="$title" 
    :seoTitle="$seoTitle" 
    :seoDescription="$seoDescription">
    <!-- Content -->
</x-shared.components.layout.base>
```

### Form Components

#### Input Component
**File:** `resources/views/shared/components/forms/input.blade.php`

**Features:**
- Multiple input types support
- Error handling and validation
- Icon support (left/right positioning)
- Help text
- Required field indicators

**Usage:**
```blade
<x-shared.components.forms.input
    name="email"
    type="email"
    label="Email Address"
    placeholder="Enter your email"
    required="true"
    :icon="'<svg>...</svg>'"
    iconPosition="left"
    helpText="We'll never share your email"
/>
```

#### Select Component
**File:** `resources/views/shared/components/forms/select.blade.php`

**Features:**
- Single and multiple selection
- Grouped options support
- Empty option customization
- Error handling

**Usage:**
```blade
<x-shared.components.forms.select
    name="category"
    label="Job Category"
    :options="[
        'tech' => 'Technology',
        'design' => 'Design',
        'marketing' => 'Marketing'
    ]"
    placeholder="Select a category"
    required="true"
/>
```

#### Button Component
**File:** `resources/views/shared/components/forms/button.blade.php`

**Features:**
- Multiple variants (primary, secondary, success, danger, etc.)
- Different sizes (xs, sm, md, lg, xl)
- Loading states
- Icon support
- Link and button modes

**Usage:**
```blade
<x-shared.components.forms.button
    variant="primary"
    size="lg"
    href="{{ route('jobs.create') }}"
    :icon="'<svg>...</svg>'"
    iconPosition="left">
    Create Job
</x-shared.components.forms.button>
```

### Table Component
**File:** `resources/views/shared/components/table/index.blade.php`

**Features:**
- Responsive tables
- Sortable headers
- Action buttons/links
- Empty state handling
- Loading states
- Striped and hoverable rows

**Usage:**
```blade
<x-shared.components.table.index
    :headers="[
        ['label' => 'Name', 'sortable' => true],
        ['label' => 'Email'],
        ['label' => 'Created']
    ]"
    :rows="$users->map(fn($user) => [
        $user->name,
        $user->email,
        $user->created_at->format('M d, Y')
    ])->toArray()"
    :actions="[
        [
            'type' => 'link',
            'label' => 'Edit',
            'url' => fn($row, $index) => route('users.edit', $users[$index]->id),
            'color' => 'blue'
        ],
        [
            'type' => 'form',
            'label' => 'Delete',
            'url' => fn($row, $index) => route('users.destroy', $users[$index]->id),
            'method' => 'DELETE',
            'color' => 'red',
            'confirm' => 'Are you sure?'
        ]
    ]"
/>
```

### Navigation Components

#### Breadcrumbs Component
**File:** `resources/views/shared/components/navigation/breadcrumbs.blade.php`

**Usage:**
```blade
<x-shared.components.navigation.breadcrumbs 
    :breadcrumbs="[
        ['title' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['title' => 'Users', 'url' => route('admin.users.index')],
        ['title' => 'Create User', 'url' => '#']
    ]"
/>
```

## Backend Layout

### Main Backend Layout
**File:** `resources/views/backend/layouts/app.blade.php`

**Features:**
- Sidebar navigation with collapsible sections
- Top navigation bar with user menu
- Breadcrumbs integration
- Mobile responsive design
- Alpine.js integration for interactivity

**Usage:**
```blade
<x-backend.layouts.app 
    title="Dashboard" 
    header="Admin Dashboard"
    :breadcrumbs="[
        ['title' => 'Dashboard', 'url' => route('admin.dashboard')]
    ]">
    <!-- Page content -->
</x-backend.layouts.app>
```

### Backend Dashboard Example
**File:** `resources/views/backend/dashboard/index.blade.php`

Demonstrates:
- Stats cards with icons
- Data tables using shared table component
- Quick action buttons
- Responsive grid layouts

## Frontend Layout

### Main Frontend Layout
**File:** `resources/views/frontend/layouts/app.blade.php`

**Features:**
- Modern navigation with dropdown menus
- Language switcher
- User authentication menu
- Footer with social links
- Mobile responsive design
- SEO optimization

**Usage:**
```blade
<x-frontend.layouts.app 
    title="Job Portal" 
    :seoTitle="'Find Your Dream Job'"
    :seoDescription="'Browse thousands of job opportunities'"
    :breadcrumbs="$breadcrumbs ?? null">
    <!-- Page content -->
</x-frontend.layouts.app>
```

### Frontend Home Page Example
**File:** `resources/views/frontend/home/index.blade.php`

Demonstrates:
- Hero section with search form
- Featured content sections
- Card-based layouts
- Call-to-action sections
- Form integration

## Component Naming Convention

### Shared Components
Use the `x-shared.components.*` namespace:
```blade
<x-shared.components.forms.input />
<x-shared.components.table.index />
<x-shared.components.navigation.breadcrumbs />
```

### Layout Components
Use the `x-backend.layouts.*` or `x-frontend.layouts.*` namespace:
```blade
<x-backend.layouts.app />
<x-frontend.layouts.app />
```

## Best Practices

### 1. Component Props
Always define props with proper defaults:
```blade
@props([
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'class' => ''
])
```

### 2. Error Handling
Include proper error handling in form components:
```blade
@php
    $hasError = $error || $errors->has($name);
    $errorMessage = $error ?? $errors->first($name);
@endphp

@if($hasError)
    <p class="text-sm text-red-600">{{ $errorMessage }}</p>
@endif
```

### 3. Accessibility
Always include proper ARIA labels and semantic HTML:
```blade
<label for="{{ $id }}" class="sr-only">{{ $label }}</label>
<input 
    id="{{ $id }}"
    aria-describedby="{{ $hasError ? $id . '-error' : '' }}"
    aria-invalid="{{ $hasError ? 'true' : 'false' }}"
/>
```

### 4. Responsiveness
Use Tailwind CSS responsive utilities:
```blade
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
```

### 5. Internationalization
Always use translation helpers:
```blade
{{ __('messages.welcome') }}
{{ trans_choice('messages.item_count', $count) }}
```

## Migration Strategy

### Phase 1: Create New Structure
1. ✅ Create new directory structure
2. ✅ Build shared components
3. ✅ Create layout templates

### Phase 2: Migrate Existing Views
1. Move admin views to `backend/` directory
2. Move public views to `frontend/` directory
3. Update component references
4. Test functionality

### Phase 3: Optimize and Enhance
1. Add more shared components as needed
2. Optimize performance
3. Add more features to existing components
4. Update documentation

## Benefits

### 1. Maintainability
- Clear separation of concerns
- Reusable components reduce code duplication
- Consistent design patterns

### 2. Scalability
- Easy to add new features
- Component-based architecture
- Flexible and extensible

### 3. Developer Experience
- Consistent API across components
- Type hints through props
- Better organization and discoverability

### 4. Performance
- Shared components reduce bundle size
- Optimized loading strategies
- Better caching potential

### 5. Design Consistency
- Standardized UI components
- Consistent styling
- Unified user experience

## Troubleshooting

### Common Issues

1. **Component not found**
   - Check the namespace is correct
   - Ensure the file exists in the right location
   - Verify the component name matches the file name

2. **Props not working**
   - Check the `@props` declaration
   - Ensure prop names match exactly
   - Verify data types are correct

3. **Styling issues**
   - Ensure Tailwind CSS is properly loaded
   - Check for class conflicts
   - Verify responsive breakpoints

### Debug Tips

1. Use `{{ dd($variable) }}` to debug prop values
2. Check browser developer tools for CSS issues
3. Use Laravel's `php artisan view:clear` to clear view cache
4. Enable debug mode for detailed error messages

## Future Enhancements

### Planned Features
1. Dark mode support
2. More form components (checkbox, radio, file upload)
3. Chart and graph components
4. Modal and popup components
5. Notification toast components
6. Loading skeleton components

### Performance Optimizations
1. Component lazy loading
2. CSS purging optimization
3. JavaScript tree shaking
4. Image optimization components

This new structure provides a solid foundation for building maintainable, scalable, and consistent user interfaces across the entire application. 