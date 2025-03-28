# Laravel Jobs Portal

A modern job portal application built with Laravel and Livewire.

## Features

- Responsive job listing and search
- Candidate and employer profiles
- Job application tracking
- Advanced filtering and search
- Multi-language support (English, Lithuanian)
- Custom Livewire datatable implementation
- Tailwind CSS styling

## Installation

1. Clone the repository:
```bash
git clone https://github.com/goleaf/laravel-jobs-portal.git
cd laravel-jobs-portal
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Configure your database in `.env`

6. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

7. Build assets:
```bash
npm run dev
```

8. Start the server:
```bash
php artisan serve
```

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
