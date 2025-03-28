# JobPortal

A modern job portal application built with Laravel, Livewire, Alpine.js and Tailwind CSS.

## Modernization Efforts

The application has undergone significant modernization work to improve performance, maintainability, and user experience:

### Front-end Improvements

1. **Alpine.js Integration**
   - Added Alpine.js for reactive UI components
   - Created modern modal system using Alpine.js
   - Implemented dropdown components with Alpine.js

2. **SVG Icon Components**
   - Converted inline SVGs to reusable Blade components
   - Created standardized icon system for consistent UI
   - Added utility to automatically convert inline SVGs to components

3. **Modern JavaScript**
   - Replaced jQuery with native JavaScript
   - Implemented modern ES6+ features
   - Created utility command to standardize JavaScript code
   - Added proper event handling with native APIs

4. **Tailwind CSS**
   - Enhanced UI with Tailwind CSS
   - Created reusable button and UI components
   - Implemented responsive design patterns

### Back-end Improvements

1. **Custom Table Components**
   - Replaced Rappasoft's table implementation with custom Livewire components
   - Added filterable and sortable functionality
   - Created reusable base table component

2. **Translation Management**
   - Enhanced translation capabilities
   - Added Lithuanian language support
   - Created utilities for standardizing translations

3. **Memory Optimization**
   - Identified and fixed memory-intensive operations
   - Optimized large data processing

## Features

- Job listings with search and filter capabilities
- Company profiles and management
- User authentication and roles
- Multilingual support
- Responsive design

## Getting Started

### Prerequisites

- PHP 8.0+
- Composer
- Node.js and NPM
- MySQL or PostgreSQL

### Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `npm install && npm run dev`
7. Serve the application with `php artisan serve`

## Available Commands

- `php artisan svg:convert` - Convert inline SVGs to components
- `php artisan js:standardize` - Standardize JavaScript code
- `php artisan standardize:translations` - Standardize translations across languages

## License

This project is licensed under the MIT License.

## Translation Management

The application supports multiple languages. Translations are managed in a single file per language.

### Adding a New Language

1. Add the language to `config/app.php` in the `available_locales` array:
```php
'available_locales' => [
    'en' => [
        'name' => 'English',
        'script' => 'Latn',
        'native' => 'English',
        'regional' => 'en_US',
    ],
    'lt' => [
        'name' => 'Lithuanian',
        'script' => 'Latn',
        'native' => 'Lietuvių',
        'regional' => 'lt_LT',
    ],
    // Add your new language here
],
```

2. Create a new translation file:
```bash
php -d memory_limit=-1 artisan translations:create-lithuanian
```

3. Update the newly created file with translations.

## Custom Livewire Tables

This project uses custom Livewire tables instead of external packages. The implementation is in:

- `app/Livewire/Components/DataTable.php` - Base component
- `app/Livewire/Components/Column.php` - Column definition
- `app/Livewire/Components/Filter.php` - Filter definition
- `resources/views/livewire/components/data-table.blade.php` - Table layout
- Various filter components in `resources/views/livewire/components/filters/`

### Creating a New Table Component

1. Create a new Livewire component that extends the DataTable class:
```php
php artisan make:livewire TableName
```

2. Extend the DataTable component:
```php
namespace App\Livewire;

use App\Livewire\Components\Column;
use App\Livewire\Components\DataTable;
use Illuminate\Database\Eloquent\Builder;

class TableName extends DataTable
{
    // Define your properties
    public string $tableName = 'table-name';
    public bool $showButtonOnHeader = true;
    public bool $showFilterOnHeader = true;
    
    // Initialize component properties
    protected function initializeComponent()
    {
        $this->filterComponents = ['path.to.filter.component'];
        $this->sortField = 'created_at';
        $this->sortDirection = 'desc';
    }
    
    // Define columns
    public function columns(): array
    {
        return [
            Column::make('Title', 'field')
                ->sortable()
                ->searchable()
                ->view('path.to.view.component'),
            // More columns...
        ];
    }
    
    // Define query builder
    public function builder(): Builder
    {
        return Model::query();
    }
}
```

## SVG Components

SVGs are extracted into components in the `resources/views/components/icons` directory. Use them in your Blade templates:

```blade
<x-icons.icon-name />
```

To extract SVGs from templates into components:

```bash
php -d memory_limit=-1 artisan svg:extract
```

## SVG Icon System

We've implemented a comprehensive SVG icon system for consistent, maintainable, and accessible icons throughout the application.

### Key Features

- **Blade Components**: All icons are implemented as Blade components
- **Centralized Management**: Icons are stored in `resources/views/components/icons/`
- **Customizable**: Icons accept attributes for customization (size, color, etc.)
- **Developer Tools**: Icon viewer available at `/icons` in local environment
- **Documentation**: See `ICONS.md` for detailed documentation

### Using Icons

```blade
<!-- Basic usage -->
<x-icons.home />

<!-- Custom size and color -->
<x-icons.user class="w-8 h-8 text-blue-500" />

<!-- With additional attributes -->
<x-icons.bell id="notification-icon" data-count="5" />
```

### Available Icons

The library includes over 20 icons for common UI elements including:

- Navigation icons (arrows, chevrons)
- Action icons (edit, trash, plus)
- Interface icons (search, filter, spinner)
- Content icons (user, document, calendar)
- And more...

To see all available icons, visit `/icons` in your local environment.

# Aire Forms Integration

This project uses [Aire](https://airephp.com/) - a modern Laravel form builder with a focus on expressive and beautiful code. Aire generates form markup that is styled with Tailwind CSS.

## Available Form Examples

The following form examples demonstrate different capabilities of Aire:

- **Contact Form** (`/contact`) - Basic form with Aire styling
- **Validation Example** (`/forms/validation`) - Client-side validation with Aire
- **Alpine.js Integration** (`/forms/alpine`) - Dynamic forms with Alpine.js
- **Binding Example** (`/forms/binding`) - Data binding capabilities
- **Error Handling** (`/forms/errors`) - Server-side validation and error summaries
- **Method Spoofing** (`/forms/methods`) - HTTP verb spoofing for PUT/PATCH/DELETE

## Basic Usage

Aire is available through the `Aire` facade. Here's a basic example of a form:

```php
{{ Aire::open()->route('contact.submit') }}

    {{ Aire::input('name', 'Your Name')
        ->required()
        ->placeholder('Enter your name') }}
    
    {{ Aire::email('email', 'Email Address')
        ->required() }}
    
    {{ Aire::textarea('message', 'Your Message')
        ->rows(5) }}
    
    {{ Aire::submit('Send Message') }}

{{ Aire::close() }}
```

## Key Features

1. **Fluent API** - All method calls are fluent, allowing for easy configuration
2. **Data Binding** - Binds data from Eloquent models, arrays, or objects
3. **Validation** - Client-side and server-side validation with error display
4. **Method Spoofing** - Automatically adds Laravel's `_method` field for non-GET/POST forms
5. **CSRF Protection** - Automatic CSRF token for non-GET forms
6. **Error Display** - Automatically displays validation errors
7. **Tailwind CSS** - Styled with Tailwind CSS by default
8. **Accessibility** - Built with accessibility in mind, with support for ARIA attributes
9. **File Uploads** - Support for single and multiple file uploads
10. **Internationalization** - Integration with Laravel's translation system

## Implementation Details

Our implementation includes:

1. **Custom Styled Forms** - All forms use Tailwind CSS for consistent styling
2. **Integration with Alpine.js** - Dynamic form behavior powered by Alpine.js
3. **Validation Examples** - Both client-side and server-side validation 
4. **Custom Configuration** - Tailwind-specific styling in `config/aire.php`
5. **Error Handling** - Comprehensive error display and summaries
6. **Complex Layouts** - Support for multi-column, sectioned, and wizard-style forms
7. **Accessibility Features** - ARIA attributes, focus management, and screen reader support
8. **Internationalization** - Translation support for multi-language applications

## Form Examples Explained

### Contact Form
A basic contact form implementation with name, email, subject and message fields.

### Validation Example
Demonstrates client-side validation with immediate feedback to users as they type.

### Alpine.js Integration
Shows how to create dynamic forms that change based on user input, using Alpine.js for reactivity.

### Binding Example
Demonstrates how Aire can bind data from various sources (arrays, objects, models) to pre-fill form fields.

### Error Handling
Shows how Aire automatically displays validation errors from Laravel's validation system.

### Method Spoofing
Demonstrates how Aire handles HTTP method spoofing for PUT, PATCH and DELETE requests, automatically adding the necessary hidden fields.

## Advanced Usage

### File Uploads

Aire supports file upload fields with custom styling:

```php
{{ Aire::file('document', 'Upload Document')
    ->accept('.pdf,.doc,.docx')
    ->helpText('Max file size: 5MB') }}
```

### Multi-Column Forms

Create responsive layouts with Tailwind's grid system:

```php
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{ Aire::input('first_name', 'First Name') }}
    {{ Aire::input('last_name', 'Last Name') }}
</div>
```

### Wizard Forms

Combine Aire with Alpine.js for multi-step forms:

```php
<div x-data="{ step: 1 }">
    <div x-show="step === 1">
        <!-- Step 1 fields -->
        <button @click="step++">Next</button>
    </div>
    <div x-show="step === 2">
        <!-- Step 2 fields -->
        <button @click="step--">Back</button>
        {{ Aire::submit('Submit') }}
    </div>
</div>
```

## Data Binding

Aire will automatically bind old input and model data:

```php
// Bind an Eloquent model
{{ Aire::bind($user) }}

// Bind an array
{{ Aire::bind(['name' => 'John Doe']) }}

// Bind an object
{{ Aire::bind((object) ['name' => 'John Doe']) }}
```

## Customization

The default Tailwind classes can be customized in `config/aire.php`. For more complete control, you can publish the view files with:

```bash
php artisan vendor:publish --tag=aire-views
```

## Documentation

For more detailed information, visit the [Aire Documentation](https://airephp.com/). For project-specific implementation details, see the comprehensive documentation in `docs/aires-forms.md` or view the example forms at the routes mentioned above.
