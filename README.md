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
